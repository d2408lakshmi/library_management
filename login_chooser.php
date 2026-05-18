<?php
// Login Chooser Page - Student vs Faculty
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['id'])) {
    if (isset($_SESSION['user_type'])) {
        if ($_SESSION['user_type'] == 'Student') {
            header("location: student_dashboard.php");
        } else {
            header("location: home.php");
        }
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>

    <!-- Bootstrap and Font Awesome -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="fonts/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --dark-wood: #4A3728;
            --classic-green: #5A8B5A;
            --parchment-text: #F0EAD6;
            --cream-bg: #FDFBF5;
            --dark-text: #4B4B4B;
            --student-blue: #2E86AB;
            --faculty-orange: #A23B72;
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

        .main-card {
            width: 100%;
            max-width: 900px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 60px 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-section {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .header-section .icon {
            font-size: 60px;
            color: var(--classic-green);
            margin-bottom: 20px;
        }

        .header-section h1 {
            font-family: 'Merriweather', serif;
            color: var(--dark-text);
            font-weight: 700;
            font-size: 36px;
            margin: 0;
        }

        .header-section p {
            color: #666;
            font-size: 16px;
            margin-top: 10px;
        }

        .login-options {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .login-card-option {
            flex: 1;
            min-width: 250px;
            max-width: 350px;
            padding: 40px 30px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }

        .student-option {
            background: linear-gradient(135deg, rgba(46, 134, 171, 0.1), rgba(46, 134, 171, 0.05));
            border-color: var(--student-blue);
        }

        .student-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(46, 134, 171, 0.3);
            background: linear-gradient(135deg, rgba(46, 134, 171, 0.15), rgba(46, 134, 171, 0.1));
        }

        .faculty-option {
            background: linear-gradient(135deg, rgba(162, 59, 114, 0.1), rgba(162, 59, 114, 0.05));
            border-color: var(--faculty-orange);
        }

        .faculty-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(162, 59, 114, 0.3);
            background: linear-gradient(135deg, rgba(162, 59, 114, 0.15), rgba(162, 59, 114, 0.1));
        }

        .card-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }

        .student-option .card-icon {
            color: var(--student-blue);
        }

        .faculty-option .card-icon {
            color: var(--faculty-orange);
        }

        .card-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            font-family: 'Merriweather', serif;
        }

        .student-option .card-title {
            color: var(--student-blue);
        }

        .faculty-option .card-title {
            color: var(--faculty-orange);
        }

        .card-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
            min-height: 40px;
        }

        .card-link {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .student-option .card-link {
            background-color: var(--student-blue);
        }

        .student-option .card-link:hover {
            background-color: #1f5a7f;
            color: white;
        }

        .faculty-option .card-link {
            background-color: var(--faculty-orange);
        }

        .faculty-option .card-link:hover {
            background-color: #7a1e52;
            color: white;
        }

        .footer-links {
            text-align: center;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }

        .footer-links a {
            color: var(--classic-green);
            text-decoration: none;
            font-weight: bold;
            margin: 0 15px;
        }

        .footer-links a:hover {
            text-decoration: underline;
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
        <div class="main-card">
            <div class="header-section">
                <i class="fa fa-university icon"></i>
                <h1>Library Portal</h1>
                <p>Select your login type to access the system</p>
            </div>

            <div class="login-options">
                <!-- Student Login Option -->
                <div class="login-card-option student-option">
                    <div class="card-icon">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="card-title">Student Login</div>
                    <div class="card-description">
                        Access your library account, view borrowed books, and manage your profile
                    </div>
                    <a href="student_login.php" class="card-link">Login as Student</a>
                </div>

                <!-- Faculty Login Option -->
                <div class="login-card-option faculty-option">
                    <div class="card-icon">
                        <i class="fa fa-briefcase"></i>
                    </div>
                    <div class="card-title">Faculty Login</div>
                    <div class="card-description">
                        Access the admin dashboard with full system management privileges
                    </div>
                    <a href="faculty_login.php" class="card-link">Login as Faculty</a>
                </div>
            </div>

            <div class="footer-links">
                <p style="margin: 0 0 15px 0; color: #666;">Don't have an account? <a href="register.php">Create Account</a></p>
                <a href="forgot_password.php"><i class="fa fa-key"></i> Forgot Password?</a>
            </div>
        </div>
    </div>
    
    <div class="login-footer-text">
        <p>© <?php echo date('Y'); ?> Library Management System</p>
    </div>
</body>
</html>
