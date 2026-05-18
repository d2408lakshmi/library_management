import os
from fastapi import FastAPI, UploadFile, File, Form, HTTPException
from fastapi.middleware.cors import CORSMiddleware
import mysql.connector
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import google.generativeai as genai

app = FastAPI(title="Library AI Service")

# Allow requests from PHP frontend
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# MySQL connection helper
def get_db_connection():
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            user='root',
            password='',
            database='project_library'
        )
        return connection
    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return None

# Configure Gemini if key is provided (either from env or passed in call)
GEMINI_API_KEY = os.environ.get("GEMINI_API_KEY")
if GEMINI_API_KEY:
    genai.configure(api_key=GEMINI_API_KEY)

@app.get("/")
def read_root():
    return {"message": "AI Library Service is running."}

@app.get("/api/recommend")
def recommend_books(user_id: int):
    # 1. Fetch user's borrowed books
    db = get_db_connection()
    if not db:
        raise HTTPException(status_code=500, detail="Database connection failed")
    
    cursor = db.cursor(dictionary=True)
    
    # Query all books
    cursor.execute("SELECT book_id, book_title, category, author FROM book")
    all_books = cursor.fetchall()
    
    # Query books borrowed by user
    cursor.execute("SELECT book_id FROM borrow_book WHERE user_id = %s", (user_id,))
    borrowed_books = [row['book_id'] for row in cursor.fetchall()]
    db.close()
    
    if not all_books:
        return {"recommendations": []}
        
    df = pd.DataFrame(all_books)
    df['category'] = df['category'].fillna('')
    df['author'] = df['author'].fillna('')
    # Create a feature string for TF-IDF
    df['features'] = df['category'] + " " + df['author']
    
    # Simple content-based filtering using category and author
    tfidf = TfidfVectorizer(stop_words='english')
    tfidf_matrix = tfidf.fit_transform(df['features'])
    cosine_sim = cosine_similarity(tfidf_matrix, tfidf_matrix)
    
    recommendations = []
    
    # If user hasn't borrowed anything, recommend random/popular books
    if not borrowed_books:
        # Just return 3 random books for now
        sampled = df.sample(min(3, len(df)))
        for _, row in sampled.iterrows():
            recommendations.append({"book_id": row['book_id'], "title": row['book_title'], "category": row['category']})
        return {"recommendations": recommendations, "note": "Randomized due to lack of history"}

    # Find similar books
    similar_indices = set()
    for book_id in borrowed_books:
        # Get matrix index for this book
        idx_df = df[df['book_id'] == book_id]
        if not idx_df.empty:
            idx = idx_df.index[0]
            # Get similarity scores for this book
            sim_scores = list(enumerate(cosine_sim[idx]))
            # Sort by similarity
            sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)
            # Take top 3 similar
            top_similar = [i[0] for i in sim_scores[1:4]]
            similar_indices.update(top_similar)
            
    # Compile final list
    for idx in similar_indices:
        book_id = int(df.iloc[idx]['book_id'])
        if book_id not in borrowed_books: # Don't recommend books they already borrowed
            recommendations.append({
                "book_id": book_id, 
                "title": df.iloc[idx]['book_title'], 
                "category": df.iloc[idx]['category'],
                "author": df.iloc[idx]['author']
            })
            if len(recommendations) >= 4:
                break
                
    return {"recommendations": recommendations}

import io
from PIL import Image

@app.post("/api/scan-book")
async def scan_book(image: UploadFile = File(...)):
    if not GEMINI_API_KEY:
        raise HTTPException(status_code=500, detail="Gemini API Key not configured")
        
    try:
        contents = await image.read()
        pil_image = Image.open(io.BytesIO(contents))
        
        model = genai.GenerativeModel('gemini-1.5-flash')
        prompt = "Extract the following details from this book cover. If a detail is missing, return an empty string for it. Return ONLY a valid JSON object with these exact keys: 'title', 'author', 'publisher_name'."
        response = model.generate_content([prompt, pil_image])
        
        # Clean up Markdown formatting from JSON response
        text_response = response.text
        if text_response.startswith("```json"):
            text_response = text_response[7:-3].strip()
        
        import json
        return json.loads(text_response)
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

from pydantic import BaseModel
class ChatRequest(BaseModel):
    message: str

@app.post("/api/chat")
def chat(request: ChatRequest):
    if not GEMINI_API_KEY:
        raise HTTPException(status_code=500, detail="Gemini API Key not configured")
        
    db = get_db_connection()
    if not db:
        raise HTTPException(status_code=500, detail="Database connection failed")
        
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT book_title, category, author, status FROM book LIMIT 50")
    books = cursor.fetchall()
    db.close()
    
    # Create simple context
    book_context = "\\n".join([f"- {b['book_title']} by {b['author']} (Category: {b['category']}, Status: {b['status']})" for b in books])
    
    system_prompt = f"You are a helpful library assistant. Here is a list of some books in our catalog:\\n{book_context}\\n\\nAnswer the user's question directly, briefly, and politely based ONLY on the books above. If the book isn't there, say you aren't sure but the user can check the search page."
    
    model = genai.GenerativeModel('gemini-1.5-flash')
    response = model.generate_content(f"{system_prompt}\\n\\nUser: {request.message}")
    
    return {"response": response.text.strip()}

@app.get("/api/forecast")
def forecast_demand():
    db = get_db_connection()
    if not db:
        raise HTTPException(status_code=500, detail="Database connection failed")
        
    cursor = db.cursor(dictionary=True)
    query = '''
    SELECT b.category, bb.date_borrowed
    FROM borrow_book bb
    JOIN book b ON bb.book_id = b.book_id
    '''
    cursor.execute(query)
    data = cursor.fetchall()
    db.close()
    
    if not data:
        return {"forecast": []}
        
    df = pd.DataFrame(data)
    df['date_borrowed'] = pd.to_datetime(df['date_borrowed'])
    df['month'] = df['date_borrowed'].dt.to_period('M')
    
    # Group by category and month
    counts = df.groupby(['category', 'month']).size().reset_index(name='count')
    
    # Simple forecast logic: (averaging past months as a naive forecast for "next month")
    # In a real model, we use statsmodels.tsa or similar.
    forecasts = []
    categories = counts['category'].unique()
    for cat in categories:
        cat_data = counts[counts['category'] == cat]
        avg_borrows = cat_data['count'].mean()
        forecasts.append({
            "category": cat,
            "predicted_demand": round(avg_borrows, 2)
        })
        
    return {"forecast": forecasts}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=5000)
