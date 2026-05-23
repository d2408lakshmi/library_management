<?php
// --- STUDENT DASHBOARD ---
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

// Find corresponding user_id in the user table to get accurate library records
$user_id = 0;
// Match the admin's firstname/lastname to the user table since they are logically separate in this schema.
$find_user = mysqli_query($con, "SELECT user_id FROM user WHERE firstname = '$firstname' AND lastname = '$lastname' LIMIT 1");
if($find_user && mysqli_num_rows($find_user) > 0) {
    $u_row = mysqli_fetch_assoc($find_user);
    $user_id = $u_row['user_id'];
}

// Get student's borrowed books
$borrowed_query = "SELECT b.book_id, b.book_title as book_name, b.author as book_author, bb.date_borrowed as issue_date, bb.due_date as return_date, 
                   DATEDIFF(NOW(), bb.date_borrowed) as days_borrowed,
                   CASE 
                     WHEN bb.due_date < NOW() THEN 'Overdue'
                     ELSE 'Active'
                   END as status
                   FROM borrow_book bb
                   JOIN book b ON bb.book_id = b.book_id
                   WHERE bb.user_id = ? AND bb.borrowed_status = 'borrowed'";

$stmt = mysqli_prepare($con, $borrowed_query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $borrowed_books = mysqli_stmt_get_result($stmt);
    $borrowed_count = mysqli_num_rows($borrowed_books);
    mysqli_stmt_close($stmt);
}

// Get total books count
$books_query = "SELECT COUNT(*) as total FROM book";
$books_result = mysqli_query($con, $books_query);
$books_row = mysqli_fetch_assoc($books_result);
$total_books = $books_row['total'] ?? 0;

// Get student's fines
$fines_query = "SELECT book_penalty FROM return_book WHERE user_id = ?";
$stmt2 = mysqli_prepare($con, $fines_query);
$total_fines = 0;
if ($stmt2) {
    mysqli_stmt_bind_param($stmt2, "i", $user_id);
    mysqli_stmt_execute($stmt2);
    $fines_result = mysqli_stmt_get_result($stmt2);
    while($row = mysqli_fetch_assoc($fines_result)) {
        if(is_numeric($row['book_penalty'])) {
            $total_fines += (float)$row['book_penalty'];
        }
    }
    mysqli_stmt_close($stmt2);
}

// Get overdue books
$overdue_query = "SELECT COUNT(*) as overdue FROM borrow_book WHERE user_id = ? AND due_date < NOW() AND borrowed_status = 'borrowed'";
$stmt3 = mysqli_prepare($con, $overdue_query);
$overdue_count = 0;
if ($stmt3) {
    mysqli_stmt_bind_param($stmt3, "i", $user_id);
    mysqli_stmt_execute($stmt3);
    $overdue_result = mysqli_stmt_get_result($stmt3);
    if($overdue_row = mysqli_fetch_assoc($overdue_result)) {
        $overdue_count = $overdue_row['overdue'];
    }
    mysqli_stmt_close($stmt3);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Library Management System</title>
    
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
        }

        .student-header {
            background: linear-gradient(135deg, var(--student-blue), #1f5a7f);
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .student-header h1 {
            margin: 0;
        }

        .stats-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .stat-card {
            flex: 1;
            min-width: 200px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: var(--student-blue);
        }

        .stat-card .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 20px;
            color: var(--dark-text);
            border-bottom: 2px solid var(--student-blue);
            padding-bottom: 10px;
        }

        .borrowed-books-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .borrowed-books-table table {
            margin: 0;
            width: 100%;
        }

        .borrowed-books-table th {
            background-color: var(--student-blue);
            color: white;
            padding: 15px;
            border: none;
        }

        .borrowed-books-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
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

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        /* AI Recommendations Styling */
        .recommendations-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .recommendation-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-top: 4px solid #F39C12;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .recommendation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .recommendation-card h4 {
            margin: 10px 0 5px 0;
            font-size: 15px;
            font-weight: bold;
            color: var(--dark-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .recommendation-card p {
            color: #777;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .recommendation-card .badge {
            background-color: var(--student-blue);
            color: white;
            padding: 5px 10px;
            font-size: 11px;
            border-radius: 12px;
        }

        /* AI Chatbot Widget Styling */
        #ai-chatbot {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 9999;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
            font-family: inherit;
        }
        #ai-chatbot-header {
            background: var(--student-blue);
            color: #fff;
            padding: 12px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #ai-chatbot-body {
            height: 300px;
            padding: 10px;
            overflow-y: auto;
            background: #f9f9f9;
            display: none;
            flex-direction: column;
        }
        #ai-chatbot-messages {
            flex-grow: 1;
            overflow-y: auto;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .ai-msg { background: #e1f5fe; padding: 5px 8px; border-radius: 5px; margin-bottom: 5px; width: fit-content; max-width: 90%; }
        .user-msg { background: #c8e6c9; padding: 5px 8px; border-radius: 5px; margin-bottom: 5px; align-self: flex-end; width: fit-content; max-width: 90%; text-align: right; margin-left: auto;}
        #ai-chatbot-input {
            width: 100%;
            display: flex;
        }
        #ai-chatbot-input input {
            flex-grow: 1;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        #ai-chatbot-input button {
            background: var(--student-blue);
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 3px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 30px;">
        <!-- Header -->
        <div class="student-header">
            <div class="row">
                <div class="col-md-8">
                    <h1><i class="fa fa-graduation-cap"></i> Welcome, <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></h1>
                    <p style="margin-top: 5px;">Student Dashboard</p>
                </div>
                <div class="col-md-4" style="text-align: right; padding-top: 10px;">
                    <a href="student_profile.php" class="btn btn-light btn-sm"><i class="fa fa-user"></i> My Profile</a>
                    <a href="logout.php" class="logout-btn"><i class="fa fa-sign-out"></i> Logout</a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $borrowed_count; ?></div>
                <div class="stat-label">Books Borrowed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_books; ?></div>
                <div class="stat-label">Total Library Books</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #f0ad4e;"><?php echo $overdue_count; ?></div>
                <div class="stat-label">Overdue Books</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #d9534f;">$<?php echo number_format($total_fines, 2); ?></div>
                <div class="stat-label">Outstanding Fine</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-md-9">
                <!-- Borrowed Books Section -->
                <div class="section-title">My Borrowed Books</div>
                
                <?php if ($borrowed_count > 0): ?>
                    <div class="borrowed-books-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Book Name</th>
                                    <th>Author</th>
                                    <th>Borrowed Date</th>
                                    <th>Return Date</th>
                                    <th>Days Borrowed</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($book = mysqli_fetch_assoc($borrowed_books)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($book['book_name']); ?></td>
                                        <td><?php echo htmlspecialchars($book['book_author']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($book['issue_date'])); ?></td>
                                        <td><?php echo date('d M Y', strtotime($book['return_date'])); ?></td>
                                        <td><?php echo $book['days_borrowed']; ?> days</td>
                                        <td>
                                            <span class="status-badge <?php echo $book['status'] == 'Overdue' ? 'status-overdue' : 'status-active'; ?>">
                                                <?php echo $book['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <p><i class="fa fa-inbox" style="font-size: 40px; color: #ddd;"></i></p>
                        <p>No borrowed books at the moment.</p>
                    </div>
                <?php endif; ?>

                <!-- Smart AI Recommendations Section -->
                <div class="section-title"><i class="fa fa-star" style="color: #F39C12;"></i> Smart AI Recommendations</div>
                <div class="recommendations-container" id="recommendations-container">
                    <p style="grid-column: 1/-1; text-align: center; color: #777;">
                        <i class="fa fa-spinner fa-spin"></i> Loading dynamic recommendations...
                    </p>
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

    <!-- AI Chatbot Widget -->
    <div id="ai-chatbot">
        <div id="ai-chatbot-header" onclick="toggleChat()">
            <span><i class="fa fa-comments"></i> AI Library Assistant</span>
            <i class="fa fa-chevron-up" id="chat-toggle-icon"></i>
        </div>
        <div id="ai-chatbot-body">
            <div id="ai-chatbot-messages">
                <div class="ai-msg">Hello! I am your AI Library Assistant. Ask me about our catalog!</div>
            </div>
            <div id="ai-chatbot-input">
                <input type="text" id="chat-input" placeholder="e.g. Do you have Python books?" onkeypress="handleEnter(event)">
                <button onclick="sendChatMessage()">Send</button>
            </div>
        </div>
    </div>

    <script>
        function toggleChat() {
            var body = document.getElementById('ai-chatbot-body');
            var icon = document.getElementById('chat-toggle-icon');
            if(body.style.display === 'flex') {
                body.style.display = 'none';
                icon.className = 'fa fa-chevron-up';
            } else {
                body.style.display = 'flex';
                icon.className = 'fa fa-chevron-down';
            }
        }

        function handleEnter(e) {
            if(e.key === 'Enter') sendChatMessage();
        }

        function sendChatMessage() {
            var input = document.getElementById('chat-input');
            var msg = input.value.trim();
            if(!msg) return;

            // add user msg
            var msgs = document.getElementById('ai-chatbot-messages');
            msgs.innerHTML += '<div class="user-msg">' + msg + '</div>';
            msgs.scrollTop = msgs.scrollHeight;
            input.value = '';

            // Loading state
            var loadingId = 'loading-' + Date.now();
            msgs.innerHTML += '<div class="ai-msg" id="' + loadingId + '"><i>Thinking...</i></div>';
            msgs.scrollTop = msgs.scrollHeight;

            fetch('http://127.0.0.1:5000/api/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: msg })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById(loadingId).remove();
                if(data.response) {
                    msgs.innerHTML += '<div class="ai-msg">' + data.response + '</div>';
                } else {
                    msgs.innerHTML += '<div class="ai-msg" style="color:red">Error answering query.</div>';
                }
                msgs.scrollTop = msgs.scrollHeight;
            })
            .catch(e => {
                document.getElementById(loadingId).remove();
                msgs.innerHTML += '<div class="ai-msg" style="color:red">Connecting to AI Service Failed.</div>';
                msgs.scrollTop = msgs.scrollHeight;
            });
        }

        // Fetch AI Recommendations dynamically
        document.addEventListener("DOMContentLoaded", function() {
            var userId = <?php echo json_encode($user_id); ?>;
            fetch('http://127.0.0.1:5000/api/recommend?user_id=' + userId)
            .then(res => res.json())
            .then(data => {
                var container = document.getElementById('recommendations-container');
                if(data.recommendations && data.recommendations.length > 0) {
                    var html = '';
                    data.recommendations.forEach(book => {
                        html += `
                        <div class="recommendation-card">
                            <i class="fa fa-book fa-3x" style="color:#F39C12; margin-bottom:12px;"></i>
                            <h4 title="${book.title}">${book.title}</h4>
                            <p style="margin: 5px 0;">${book.author || 'Unknown Author'}</p>
                            <span class="badge">${book.category}</span>
                        </div>`;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #777;">No recommendations available yet.</p>';
                }
            })
            .catch(err => {
                console.error("Recommendations API failed:", err);
                document.getElementById('recommendations-container').innerHTML = 
                    '<p style="grid-column: 1/-1; text-align: center; color: red;">Failed to connect to AI Service. Ensure the Python API is running.</p>';
            });
        });
    </script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
