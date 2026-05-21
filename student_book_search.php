<?php
// --- STUDENT BOOK SEARCH ---
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

$search_title = $_POST['book_title'] ?? '0';
$search_pub = $_POST['book_pub'] ?? '0';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - Library Management System</title>
    
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

        .search-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .search-title {
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

        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 30px;">
        <!-- Header -->
        <div class="student-header">
            <div class="row">
                <div class="col-md-8">
                    <h1><i class="fa fa-search"></i> Search Books</h1>
                    <p style="margin-top: 5px;">Search and explore the library book catalog</p>
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
                <div class="search-card">
                    <div class="search-title">Search Catalog</div>
                    
                    <form method="post" action="" class="row">
                        <div class="col-md-4 form-group">
                            <label>Book Title</label>
                            <select name="book_title" class="form-control">
                                <option value="0">All Titles</option>
                                <?php
                                $titles_query = mysqli_query($con, "SELECT DISTINCT book_title FROM book ORDER BY book_title ASC");
                                while ($row = mysqli_fetch_array($titles_query)) {
                                    $selected = ($search_title == $row['book_title']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($row['book_title'])."' $selected>".htmlspecialchars($row['book_title'])."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Publisher</label>
                            <select name="book_pub" class="form-control">
                                <option value="0">All Publishers</option>
                                <?php
                                $pub_query = mysqli_query($con, "SELECT DISTINCT book_pub FROM book ORDER BY book_pub ASC");
                                while ($row = mysqli_fetch_array($pub_query)) {
                                    $selected = ($search_pub == $row['book_pub']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($row['book_pub'])."' $selected>".htmlspecialchars($row['book_pub'])."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group" style="padding-top: 25px;">
                            <button type="submit" name="search" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Search Catalog</button>
                        </div>
                    </form>

                    <?php
                    if (isset($_POST['search'])) {
                        $where_clauses = [];
                        if ($search_title !== '0') {
                            $where_clauses[] = "book_title = '" . mysqli_real_escape_string($con, $search_title) . "'";
                        }
                        if ($search_pub !== '0') {
                            $where_clauses[] = "book_pub = '" . mysqli_real_escape_string($con, $search_pub) . "'";
                        }

                        $where_sql = "";
                        if (count($where_clauses) > 0) {
                            $where_sql = "WHERE " . implode(" AND ", $where_clauses);
                        }

                        $query_str = "SELECT * FROM book $where_sql ORDER BY book_title ASC";
                        $result = mysqli_query($con, $query_str) or die(mysqli_error($con));
                        $count = mysqli_num_rows($result);

                        echo "<div style='margin-top: 20px; font-weight: bold; color: var(--student-blue);'>Total books found: $count</div>";
                        
                        if ($count > 0) {
                            ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Barcode</th>
                                            <th>Title</th>
                                            <th>Author(s)</th>
                                            <th>Publisher</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysqli_fetch_array($result)) {
                                            $authors = array_filter([$row['author'], $row['author_2'], $row['author_3'], $row['author_4'], $row['author_5']]);
                                            ?>
                                            <tr>
                                                <td><code><?php echo htmlspecialchars($row['book_barcode']); ?></code></td>
                                                <td><strong><?php echo htmlspecialchars($row['book_title']); ?></strong></td>
                                                <td><?php echo htmlspecialchars(implode(', ', $authors)); ?></td>
                                                <td><?php echo htmlspecialchars($row['book_pub']); ?></td>
                                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                                <td>
                                                    <?php if ($row['status'] == 'Available') { ?>
                                                        <span class="label label-success"><?php echo htmlspecialchars($row['status']); ?></span>
                                                    <?php } else { ?>
                                                        <span class="label label-warning"><?php echo htmlspecialchars($row['status']); ?></span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        } else {
                            echo "<div class='alert alert-warning' style='margin-top: 20px;'>No books found matching the search criteria.</div>";
                        }
                    } else {
                        // Display default recent books
                        $result = mysqli_query($con, "SELECT * FROM book ORDER BY book_id DESC LIMIT 10") or die(mysqli_error($con));
                        ?>
                        <div style="margin-top: 30px; font-weight: bold; font-size: 18px; color: var(--student-blue);">Recently Added Books</div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Barcode</th>
                                        <th>Title</th>
                                        <th>Author(s)</th>
                                        <th>Publisher</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = mysqli_fetch_array($result)) {
                                        $authors = array_filter([$row['author'], $row['author_2'], $row['author_3'], $row['author_4'], $row['author_5']]);
                                        ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($row['book_barcode']); ?></code></td>
                                            <td><strong><?php echo htmlspecialchars($row['book_title']); ?></strong></td>
                                            <td><?php echo htmlspecialchars(implode(', ', $authors)); ?></td>
                                            <td><?php echo htmlspecialchars($row['book_pub']); ?></td>
                                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                                            <td>
                                                <?php if ($row['status'] == 'Available') { ?>
                                                    <span class="label label-success"><?php echo htmlspecialchars($row['status']); ?></span>
                                                <?php } else { ?>
                                                    <span class="label label-warning"><?php echo htmlspecialchars($row['status']); ?></span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
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
