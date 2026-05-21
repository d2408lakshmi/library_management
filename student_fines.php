<?php
// --- STUDENT FINES & PENALTIES ---
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

// Find corresponding user_id in the user table
$user_id = 0;
$find_user = mysqli_query($con, "SELECT user_id FROM user WHERE firstname = '$firstname' AND lastname = '$lastname' LIMIT 1");
if ($find_user && mysqli_num_rows($find_user) > 0) {
    $user_row = mysqli_fetch_assoc($find_user);
    $user_id = $user_row['user_id'];
}

// Get student's fine history
$fines_query = "SELECT r.*, b.book_title, b.book_barcode 
                FROM return_book r 
                LEFT JOIN book b ON r.book_id = b.book_id 
                WHERE r.user_id = ? 
                ORDER BY r.return_book_id DESC";

$stmt = mysqli_prepare($con, $fines_query);
$fines_list = [];
$total_fines = 0.0;

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $fines_list[] = $row;
        if (is_numeric($row['book_penalty'])) {
            $total_fines += (float)$row['book_penalty'];
        }
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Fines - Library Management System</title>
    
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

        .fines-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .fines-title {
            font-size: 24px;
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

        .fine-banner {
            background: #fff5f5;
            border-left: 4px solid #d9534f;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .fine-banner-amount {
            font-size: 28px;
            font-weight: bold;
            color: #d9534f;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 30px;">
        <!-- Header -->
        <div class="student-header">
            <div class="row">
                <div class="col-md-8">
                    <h1><i class="fa fa-money"></i> My Fines & Penalties</h1>
                    <p style="margin-top: 5px;">View outstanding balances and transaction history for library fines</p>
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
                <div class="fines-card">
                    <div class="fines-title">Fine Summary</div>

                    <div class="fine-banner">
                        <div>
                            <h4 style="margin: 0; color: #721c24;">Total Fines Incurred</h4>
                            <small style="color: #666;">Fines incurred due to late book submissions</small>
                        </div>
                        <div class="fine-banner-amount">
                            Rs. <?php echo number_format($total_fines, 2); ?>
                        </div>
                    </div>

                    <div class="fines-title" style="font-size: 20px; margin-top: 30px;">Fine History / Return Logs</div>
                    
                    <?php if (count($fines_list) > 0) { ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Book Title</th>
                                        <th>Barcode</th>
                                        <th>Date Borrowed</th>
                                        <th>Due Date</th>
                                        <th>Date Returned</th>
                                        <th>Fine (Rs.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fines_list as $row) { ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($row['book_title'] ?? 'N/A'); ?></strong></td>
                                            <td><code><?php echo htmlspecialchars($row['book_barcode'] ?? 'N/A'); ?></code></td>
                                            <td><?php echo date('M d, Y', strtotime($row['date_borrowed'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['due_date'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['date_returned'])); ?></td>
                                            <td style="font-weight: bold; <?php echo ((float)$row['book_penalty'] > 0) ? 'color:#d9534f;' : 'color:#5cb85c;'; ?>">
                                                Rs. <?php echo number_format((float)$row['book_penalty'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-success">Great news! You have no fine records or transaction history in this account.</div>
                    <?php } ?>
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
