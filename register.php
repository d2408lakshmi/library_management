<?php
// --- PHP REGISTRATION LOGIC ---
// This block should be at the very top of the file, before any HTML.

include('include/dbcon.php');

// Start the session only if it's not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Variable to hold any error/success messages
$error_message = '';
$success_message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $user_role = isset($_POST['user_role']) ? $_POST['user_role'] : 'Student';

    // Validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (strlen($username) < 3) {
        $error_message = "Username must be at least 3 characters long.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Check if username already exists
        $check_stmt = mysqli_prepare($con, "SELECT admin_id FROM admin WHERE username = ? OR email_id = ?");
        mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Username or email already exists. Please choose another.";
        } else {
            // Insert new user into database
            $insert_stmt = mysqli_prepare($con, "INSERT INTO admin (firstname, lastname, email_id, username, password, confirm_password, admin_type, admin_added) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            mysqli_stmt_bind_param($insert_stmt, "sssssss", $firstname, $lastname, $email, $username, $password, $confirm_password, $user_role);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $success_message = "Account created successfully! You can now login.";
                // Clear form fields
                $firstname = $lastname = $email = $username = '';
                $_POST['password'] = $_POST['confirm_password'] = '';
            } else {
                $error_message = "Registration failed. Please try again.";
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Register</title>

    <!-- Bootstrap and Font Awesome -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="fonts/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Google Fonts for a more elegant look -->
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

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            padding: 15px;
        }

        .register-card {
            width: 100%;
            max-width: 450px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header .icon {
            font-size: 50px;
            color: var(--classic-green);
        }

        .register-header h2 {
            font-family: 'Merriweather', serif;
            color: var(--dark-text);
            margin-top: 15px;
            font-weight: 700;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
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

        .btn-register {
            background-color: var(--classic-green);
            border-color: var(--classic-green);
            color: white;
            padding: 10px;
            border-radius: 25px;
            font-weight: bold;
            letter-spacing: 0.5px;
            transition: background-color 0.3s ease;
        }

        .btn-register:hover {
            background-color: #4e7a4e;
            border-color: #4e7a4e;
            color: white;
        }
        
        .register-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-footer a {
            color: var(--classic-green);
            font-weight: bold;
            text-decoration: none;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 5px;
        }
        
        .register-footer-text {
            position: absolute;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-family: 'Georgia', serif;
            z-index: 2;
        }

        .form-group-row {
            display: flex;
            gap: 10px;
        }

        .form-group-row .form-group {
            flex: 1;
        }

    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <i class="fa fa-user-plus icon"></i>
                <h2>Create Account</h2>
                <p>Join our library community</p>
            </div>
            
            <form method="post" action="register.php">
                
                <!-- Display Error Message -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <!-- Display Success Message -->
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                        <br><br>
                        <a href="index.php" style="color: #155724; font-weight: bold;">Click here to login</a>
                    </div>
                <?php endif; ?>

                <!-- First Name and Last Name Row -->
                <div class="form-group-row">
                    <div class="form-group input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" name="firstname" placeholder="First Name" required value="<?php echo isset($firstname) ? htmlspecialchars($firstname) : ''; ?>">
                    </div>
                    <div class="form-group input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" name="lastname" placeholder="Last Name" required value="<?php echo isset($lastname) ? htmlspecialchars($lastname) : ''; ?>">
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>

                <!-- Username -->
                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-at"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="Username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                </div>

                <!-- User Role Selection -->
                <div class="form-group">
                    <label style="color: var(--dark-text); font-weight: bold; margin-bottom: 8px; display: block;">Select Your Role:</label>
                    <div style="display: flex; gap: 15px;">
                        <label style="flex: 1; margin: 0; cursor: pointer;">
                            <input type="radio" name="user_role" value="Student" checked style="margin-right: 8px;">
                            <i class="fa fa-graduation-cap"></i> Student
                        </label>
                        <label style="flex: 1; margin: 0; cursor: pointer;">
                            <input type="radio" name="user_role" value="Librarian" style="margin-right: 8px;">
                            <i class="fa fa-briefcase"></i> Librarian
                        </label>
                    </div>
                    <small style="color: #999; display: block; margin-top: 8px;">Note: Librarian accounts require admin approval</small>
                </div>

                <!-- Password -->
                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Password (min 6 chars)" required>
                </div>

                <!-- Confirm Password -->
                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <!-- Register Button -->
                <div class="form-group">
                    <button class="btn btn-primary btn-block btn-register" type="submit" name="register">CREATE ACCOUNT</button>
                </div>

                <!-- Footer Links -->
                <div class="register-footer">
                    Already have an account? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="register-footer-text">
        <p>© <?php echo date('Y'); ?> Library Management System</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
