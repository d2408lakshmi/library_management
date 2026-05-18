<?php
// --- PHP FACULTY LOGIN LOGIC ---
include('include/dbcon.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error_message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } else {
        // Use PREPARED STATEMENTS for security
        $stmt = mysqli_prepare($con, "SELECT admin_id, firstname, lastname, admin_type FROM admin WHERE username = ? AND password = ? AND admin_type IN ('Admin', 'Librarian')");
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        if ($row) {
            // Login successful
            $_SESSION['id'] = $row['admin_id'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];
            $_SESSION['user_type'] = $row['admin_type'];

            // Log the user's login activity
            $log_stmt = mysqli_prepare($con, "INSERT INTO user_log (firstname, lastname, admin_type, date_log) VALUES (?, ?, ?, NOW())");
            mysqli_stmt_bind_param($log_stmt, "sss", $row['firstname'], $row['lastname'], $row['admin_type']);
            mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);

            // Redirect to admin dashboard
            header("location: home.php");
            exit();
        } else {
            $error_message = "Invalid username or password for faculty account.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Faculty Login</title>

    <!-- Bootstrap and Font Awesome -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="fonts/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --faculty-orange: #A23B72;
            --dark-text: #4B4B4B;
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Lato', sans-serif;
        }

        body {
            background-image: url('images/background.jpg');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            padding: 15px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-in-out;
            border-top: 4px solid var(--faculty-orange);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header .icon {
            font-size: 50px;
            color: var(--faculty-orange);
        }

        .login-header h2 {
            font-family: 'Merriweather', serif;
            color: var(--dark-text);
            margin-top: 15px;
            font-weight: 700;
        }

        .login-header .badge {
            background-color: var(--faculty-orange);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
            display: inline-block;
        }
        
        .form-control {
            height: 45px;
            border-radius: 25px;
            padding-left: 50px;
            border: 1px solid #ddd;
        }
        
        .input-group-addon {
            position: absolute;
            left: 1px;
            top: 1px;
            bottom: 1px;
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #aaa;
            font-size: 16px;
        }
        
        .input-group {
            position: relative;
        }

        .btn-login {
            background-color: var(--faculty-orange);
            border-color: var(--faculty-orange);
            color: white;
            padding: 10px;
            border-radius: 25px;
            font-weight: bold;
            letter-spacing: 0.5px;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #7a1e52;
            border-color: #7a1e52;
            color: white;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-footer a {
            color: var(--faculty-orange);
            font-weight: bold;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .back-link {
            text-align: left;
            margin-bottom: 20px;
        }

        .back-link a {
            color: var(--faculty-orange);
            text-decoration: none;
            font-size: 14px;
        }

        .alert {
            border-radius: 5px;
        }

        .login-footer-text {
            position: absolute;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-family: 'Georgia', serif;
            z-index: 2;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="back-link">
                <a href="login_chooser.php"><i class="fa fa-arrow-left"></i> Back to Login Options</a>
            </div>

            <div class="login-header">
                <i class="fa fa-briefcase icon"></i>
                <h2>Faculty Portal</h2>
                <span class="badge">Admin Access</span>
            </div>
            
            <form method="post" action="faculty_login.php">
                
                <!-- Display Error Message -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
                </div>
                
                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary btn-block btn-login" type="submit" name="login">SIGN IN</button>
                </div>

                <div class="login-footer">
                    <a href="forgot_password.php"><i class="fa fa-key"></i> Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="login-footer-text">
        <p>© <?php echo date('Y'); ?> Library Management System</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
