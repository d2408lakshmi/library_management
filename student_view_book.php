<?php
include('include/dbcon.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if student is logged in
if (!isset($_SESSION['id']) || $_SESSION['user_type'] != 'Student') {
    header("location: student_login.php");
    exit();
}

$student_id = $_SESSION['id'];
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];

if (!isset($_GET['book_id'])) {
    header("location: student_book_search.php");
    exit();
}

$book_id = $_GET['book_id'];
$book_query = mysqli_query($con, "SELECT * FROM book WHERE book_id = '" . mysqli_real_escape_string($con, $book_id) . "'");
$book = mysqli_fetch_array($book_query);

if (!$book) {
    echo "Book not found.";
    exit();
}

$authors = array_filter([$book['author'], $book['author_2'], $book['author_3'], $book['author_4'], $book['author_5']]);
$author_list = implode(', ', $authors);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details - <?php echo htmlspecialchars($book['book_title']); ?></title>
    
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="fonts/css/font-awesome.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        :root {
            --student-blue: #2E86AB;
            --dark-text: #4B4B4B;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Lato', sans-serif;
        }

        .student-header {
            background: linear-gradient(135deg, var(--student-blue), #1f5a7f);
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .book-details-card, .ai-guide-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 20px;
            font-weight: bold;
            color: var(--student-blue);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--student-blue);
            padding-bottom: 10px;
        }

        .sidebar-nav {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .sidebar-nav a {
            display: block;
            padding: 15px;
            color: var(--dark-text);
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar-nav a:hover {
            background-color: #f5f5f5;
            border-left-color: var(--student-blue);
            color: var(--student-blue);
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #c82333;
            color: white;
        }

        /* Quiz styles */
        .quiz-question {
            background-color: #fcfcfc;
            border: 1px solid #eef;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .quiz-option {
            margin: 8px 0;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .quiz-option:hover {
            background-color: #f0f8ff;
        }

        .quiz-option.selected {
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }

        .quiz-option.correct {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .quiz-option.incorrect {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .concept-badge {
            background-color: #e2f0d9;
            color: #385723;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 30px;">
        <!-- Header -->
        <div class="student-header">
            <div class="row">
                <div class="col-md-8">
                    <h1><i class="fa fa-book"></i> Book Details</h1>
                    <p style="margin-top: 5px;">View library catalog entry & generate AI Study Guide</p>
                </div>
                <div class="col-md-4" style="text-align: right; padding-top: 10px;">
                    <a href="student_book_search.php" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back to Search</a>
                    <a href="logout.php" class="logout-btn"><i class="fa fa-sign-out"></i> Logout</a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-md-9">
                <!-- Book Info -->
                <div class="book-details-card">
                    <div class="card-title"><i class="fa fa-info-circle"></i> Catalog Information</div>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <?php if($book['book_image'] != ""): ?>
                                <img src="upload/<?php echo $book['book_image']; ?>" class="img-responsive img-thumbnail" style="max-height: 250px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                            <?php else: ?>
                                <img src="images/book_image.jpg" class="img-responsive img-thumbnail" style="max-height: 250px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h2 style="margin-top:0; color:#333; font-weight:bold;"><?php echo htmlspecialchars($book['book_title']); ?></h2>
                            <table class="table table-striped" style="margin-top: 20px;">
                                <tr>
                                    <th style="width: 150px;">Author(s)</th>
                                    <td><?php echo htmlspecialchars($author_list); ?></td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td><span class="label label-primary" style="background-color: var(--student-blue);"><?php echo htmlspecialchars($book['category']); ?></span></td>
                                </tr>
                                <tr>
                                    <th>Publisher</th>
                                    <td><?php echo htmlspecialchars($book['book_pub']); ?></td>
                                </tr>
                                <tr>
                                    <th>Publisher Name</th>
                                    <td><?php echo htmlspecialchars($book['publisher_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>ISBN</th>
                                    <td><code><?php echo htmlspecialchars($book['isbn']); ?></code></td>
                                </tr>
                                <tr>
                                    <th>Barcode</th>
                                    <td><code><?php echo htmlspecialchars($book['book_barcode']); ?></code></td>
                                </tr>
                                <tr>
                                    <th>Availability Status</th>
                                    <td>
                                        <?php if ($book['status'] == 'Available') { ?>
                                            <span class="label label-success"><?php echo htmlspecialchars($book['status']); ?></span>
                                        <?php } else { ?>
                                            <span class="label label-warning"><?php echo htmlspecialchars($book['status']); ?></span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- AI Study Guide widget -->
                <div class="ai-guide-card">
                    <div class="card-title" style="border-bottom-color: #F39C12; color: #F39C12;">
                        <i class="fa fa-graduation-cap"></i> AI Study Guide & Smart Quiz
                    </div>
                    
                    <div id="ai-initial-state" class="text-center" style="padding: 20px 0;">
                        <p style="color: #666; font-size: 15px;">Need help studying this book? Let our AI generate a custom syllabus summary, key learning topics, and a self-assessment practice quiz.</p>
                        <button type="button" class="btn btn-warning btn-lg" id="btn-generate-guide" style="font-weight: bold; background-color: #F39C12; border-color: #F39C12; color: white; margin-top: 10px;">
                            <i class="fa fa-magic"></i> Generate Study Guide
                        </button>
                    </div>

                    <div id="ai-loading" style="display: none; text-align: center; padding: 30px 0;">
                        <i class="fa fa-spinner fa-spin fa-3x" style="color: #F39C12; margin-bottom: 15px;"></i>
                        <p style="font-weight: bold; color: #555;">Analyzing book themes and drafting practice questions...</p>
                        <p style="font-size: 12px; color: #888;">(Powered by Gemini 1.5 Flash)</p>
                    </div>

                    <div id="ai-guide-content" style="display: none;">
                        <!-- Summary -->
                        <div style="background-color: #fff9f0; border-left: 4px solid #F39C12; padding: 15px; border-radius: 4px; margin-bottom: 25px;">
                            <h4 style="margin-top: 0; color: #D35400; font-weight: bold;"><i class="fa fa-quote-left"></i> Book Summary</h4>
                            <p id="ai-summary-text" style="font-size: 14px; line-height: 1.6; color: #555;"></p>
                        </div>

                        <!-- Core Concepts -->
                        <h4 style="font-weight: bold; color: #333; margin-bottom: 15px;"><i class="fa fa-list-ul"></i> Core Learning Topics</h4>
                        <div id="ai-concepts-list" style="margin-bottom: 30px;">
                        </div>

                        <!-- Interactive Quiz -->
                        <div style="border-top: 1px solid #eee; padding-top: 25px;">
                            <h4 style="font-weight: bold; color: #333; margin-bottom: 20px;"><i class="fa fa-question-circle"></i> AI Practice Quiz</h4>
                            <div id="ai-quiz-container">
                            </div>
                            <div id="quiz-results" class="alert alert-success" style="display: none; font-weight: bold; margin-top: 15px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="sidebar-nav">
                    <a href="student_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
                    <a href="student_book_search.php"><i class="fa fa-search"></i> Search Books</a>
                    <a href="student_profile.php"><i class="fa fa-user"></i> My Profile</a>
                    <a href="student_fines.php"><i class="fa fa-money"></i> My Fines</a>
                    <a href="logout.php" style="border-left-color: #dc3545; color: #dc3545;"><i class="fa fa-sign-out"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-generate-guide').addEventListener('click', generateGuide);

        var quizQuestions = [];
        var selectedAnswers = {};

        function generateGuide() {
            var btn = document.getElementById('btn-generate-guide');
            var initialState = document.getElementById('ai-initial-state');
            var loading = document.getElementById('ai-loading');
            var content = document.getElementById('ai-guide-content');

            initialState.style.display = 'none';
            loading.style.display = 'block';

            var title = <?php echo json_encode($book['book_title']); ?>;
            var author = <?php echo json_encode($author_list); ?>;
            var category = <?php echo json_encode($book['category']); ?>;

            var url = 'http://127.0.0.1:5000/api/summarize-book?title=' + encodeURIComponent(title) + 
                      '&author=' + encodeURIComponent(author) + 
                      '&category=' + encodeURIComponent(category);

            fetch(url)
            .then(res => {
                if(!res.ok) throw new Error("Backend response not OK");
                return res.json();
            })
            .then(data => {
                // Populate summary
                document.getElementById('ai-summary-text').innerText = data.summary || "No summary available.";

                // Populate concepts
                var conceptsContainer = document.getElementById('ai-concepts-list');
                conceptsContainer.innerHTML = '';
                if(data.core_concepts && data.core_concepts.length > 0) {
                    data.core_concepts.forEach(concept => {
                        var div = document.createElement('div');
                        div.style.marginBottom = '10px';
                        div.innerHTML = `<span class="concept-badge"><i class="fa fa-check"></i> Topic</span> <span style="font-size:14px; color:#555; margin-left: 8px;">${concept}</span>`;
                        conceptsContainer.appendChild(div);
                    });
                } else {
                    conceptsContainer.innerHTML = '<p>No topics listed.</p>';
                }

                // Populate Quiz
                quizQuestions = data.study_questions || [];
                selectedAnswers = {};
                var quizContainer = document.getElementById('ai-quiz-container');
                quizContainer.innerHTML = '';

                if(quizQuestions.length > 0) {
                    quizQuestions.forEach((q, qIndex) => {
                        var questionDiv = document.createElement('div');
                        questionDiv.className = 'quiz-question';
                        
                        var questionTitle = document.createElement('h5');
                        questionTitle.style.fontWeight = 'bold';
                        questionTitle.innerText = `Q${qIndex + 1}: ${q.question}`;
                        questionDiv.appendChild(questionTitle);

                        var optionsContainer = document.createElement('div');
                        q.options.forEach(opt => {
                            var optionDiv = document.createElement('div');
                            optionDiv.className = 'quiz-option';
                            optionDiv.innerText = opt;
                            optionDiv.onclick = function() {
                                selectOption(qIndex, opt, optionDiv, optionsContainer);
                            };
                            optionsContainer.appendChild(optionDiv);
                        });
                        questionDiv.appendChild(optionsContainer);
                        
                        quizContainer.appendChild(questionDiv);
                    });

                    // Add submit button
                    var submitBtn = document.createElement('button');
                    submitBtn.type = 'button';
                    submitBtn.id = 'btn-submit-quiz';
                    submitBtn.className = 'btn btn-primary';
                    submitBtn.style.fontWeight = 'bold';
                    submitBtn.style.marginTop = '10px';
                    submitBtn.innerText = 'Submit Answers';
                    submitBtn.onclick = checkQuizAnswers;
                    quizContainer.appendChild(submitBtn);
                } else {
                    quizContainer.innerHTML = '<p>No study questions generated.</p>';
                }

                // Display content
                loading.style.display = 'none';
                content.style.display = 'block';
            })
            .catch(err => {
                console.error("AI Guide generation failed:", err);
                alert("Failed to generate AI Study Guide. Ensure your AI Service is active and has a valid GEMINI_API_KEY.");
                initialState.style.display = 'block';
                loading.style.display = 'none';
            });
        }

        function selectOption(qIndex, value, optionEl, parentEl) {
            // Check if already submitted
            if(document.getElementById('btn-submit-quiz').disabled) return;

            // Clear previous selection
            var options = parentEl.getElementsByClassName('quiz-option');
            for(var i=0; i<options.length; i++) {
                options[i].classList.remove('selected');
            }
            // Select new
            optionEl.classList.add('selected');
            selectedAnswers[qIndex] = value;
        }

        function checkQuizAnswers() {
            var correctCount = 0;
            var total = quizQuestions.length;

            if(Object.keys(selectedAnswers).length < total) {
                alert("Please answer all questions before submitting!");
                return;
            }

            var questionDivs = document.getElementById('ai-quiz-container').getElementsByClassName('quiz-question');

            quizQuestions.forEach((q, qIndex) => {
                var selected = selectedAnswers[qIndex];
                var correct = q.correct_answer;
                var questionDiv = questionDivs[qIndex];
                var optionDivs = questionDiv.getElementsByClassName('quiz-option');

                for(var i=0; i<optionDivs.length; i++) {
                    var el = optionDivs[i];
                    el.onclick = null; // Disable clicking
                    el.classList.remove('selected');

                    if(el.innerText === correct) {
                        el.classList.add('correct');
                    } else if(el.innerText === selected && selected !== correct) {
                        el.classList.add('incorrect');
                    }
                }

                if(selected === correct) {
                    correctCount++;
                }
            });

            // Disable submit button
            document.getElementById('btn-submit-quiz').disabled = true;

            // Show results summary
            var resultsDiv = document.getElementById('quiz-results');
            resultsDiv.style.display = 'block';
            resultsDiv.innerHTML = `<i class="fa fa-trophy"></i> Quiz Complete! You scored ${correctCount} out of ${total}.`;
            if(correctCount === total) {
                resultsDiv.className = 'alert alert-success';
            } else {
                resultsDiv.className = 'alert alert-warning';
            }
        }
    </script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
