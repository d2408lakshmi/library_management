<?php
// --- STUDENT PROFILE ---
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
$email = $_SESSION['email'] ?? '';

// Find corresponding user details in the user table
$user_details = null;
$find_user = mysqli_query($con, "SELECT * FROM user WHERE firstname = '$firstname' AND lastname = '$lastname' LIMIT 1");
if($find_user && mysqli_num_rows($find_user) > 0) {
    $user_details = mysqli_fetch_assoc($find_user);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Library Management System</title>
    
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

        .student-header h1 {
            margin: 0;
        }

        .profile-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .profile-title {
            font-size: 24px;
            font-weight: bold;
            color: var(--student-blue);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--student-blue);
            padding-bottom: 10px;
        }

        .info-group {
            margin-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 18px;
            color: var(--dark-text);
            margin-top: 5px;
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
    </style>
</head>
<body>
    <div class="container" style="margin-top: 30px;">
        <!-- Header -->
        <div class="student-header">
            <div class="row">
                <div class="col-md-8">
                    <h1><i class="fa fa-user"></i> My Profile</h1>
                    <p style="margin-top: 5px;">View your account registration and library details</p>
                </div>
                <div class="col-md-4" style="text-align: right; padding-top: 10px;">
                    <a href="student_dashboard.php" class="btn btn-light btn-sm"><i class="fa fa-dashboard"></i> Dashboard</a>
                    <a href="logout.php" class="logout-btn"><i class="fa fa-sign-out"></i> Logout</a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-md-9">
                <div class="profile-card">
                    <div class="profile-title">Personal Details</div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($firstname . ' ' . ($user_details['middlename'] ? $user_details['middlename'] . ' ' : '') . $lastname); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Roll Number / ID</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_details['roll_number'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($email ? $email : 'N/A'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Contact Number</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_details['contact'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Gender</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_details['gender'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Branch</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_details['branch'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-group">
                                <div class="info-label">Home Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_details['address'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Library Card Status</div>
                                <div class="info-value">
                                    <span class="label label-success" style="font-size: 14px; padding: 5px 10px;">Active</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Member Since</div>
                                <div class="info-value"><?php echo isset($user_details['user_added']) ? date('M d, Y', strtotime($user_details['user_added'])) : 'N/A'; ?></div>
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

    <script src="js/bootstrap.min.js"></script>
</body>
</html>
