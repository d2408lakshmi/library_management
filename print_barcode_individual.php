<?php
include ('include/dbcon.php');

// Start the session to access session variables
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$code = isset($_GET['code']) ? $_GET['code'] : '';

// Fetch book title associated with the barcode
$book_title = '';
if (!empty($code)) {
    // Use prepared statements for better security
    $stmt = mysqli_prepare($con, "SELECT book_title FROM book WHERE book_barcode = ?");
    mysqli_stmt_bind_param($stmt, "s", $code);
    mysqli_stmt_execute($stmt);
    $result1 = mysqli_stmt_get_result($stmt);
    
    if ($row1 = mysqli_fetch_array($result1)) {
        $book_title = $row1['book_title'];
    }
    mysqli_stmt_close($stmt);
}
?>
<html>
<head>
    <title>Library Management System - Print Barcode</title>
    <style>
        .container { width: 100%; margin: auto; }
        hr { border: solid black 1px; }
        @media print {
            #print { display: none; }
        }
        #print {
            width: 90px;
            height: 30px;
            font-size: 18px;
            background: white;
            border-radius: 4px;
            margin-left: 28px;
            cursor: pointer;
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="container">
        <div id="header">
            <center><h5 style="font-style:Calibri;">Demo Institute Of Technology - 428</h5></center>
            <center><h5 style="font-style:Calibri; margin-top:-14px;">Demo Institute Of Pharmacy - 551</h5></center>
            <center><h5 style="font-style:Calibri; margin-top:-14px;">Library Management System</h5></center>
        </div>
        <hr>
        <button type="submit" id="print" onclick="printPage()">Print</button>
        <div align="right">
            <b style="color:blue;">Date Prepared:</b> <?php echo date("l, d-m-Y"); ?>
        </div>
        <br/>
        
        <!-- === UPDATED BARCODE SECTION === -->
        <div style="text-align: left; margin-left: 28px;">
            <?php
            if (!empty($code)) {
                // Safely encode the barcode for the URL
                $barcode_safe_for_url = urlencode($code);

                // Point the image src to our new, modern generator script
                echo '<img src="generate_barcode.php?code=' . $barcode_safe_for_url . '" alt="Barcode for ' . htmlspecialchars($code) . '">';
                
                // Display the book title below the barcode
                echo "<h4>" . htmlspecialchars($book_title) . "</h4>";

            } else {
                echo "<p>No barcode specified.</p>";
            }
            ?>
        </div>
        <br />
        <br />
        
        <!-- Prepared By Section -->
        <?php
            if (isset($_SESSION['id'])) {
                $id_session = $_SESSION['id'];
                // Use prepared statement here as well for security
                $stmt_user = mysqli_prepare($con, "SELECT firstname, lastname FROM admin WHERE admin_id = ?");
                mysqli_stmt_bind_param($stmt_user, "i", $id_session);
                mysqli_stmt_execute($stmt_user);
                $user_result = mysqli_stmt_get_result($stmt_user);

                if ($row_user = mysqli_fetch_array($user_result)) {
            ?>
                    <div style="margin-left: 28px;">
                        <span style="font-weight: bold;">Prepared by:</span><br>
                        <span><?php echo htmlspecialchars($row_user['firstname'] . " " . $row_user['lastname']); ?></span>
                    </div>
            <?php
                }
                mysqli_stmt_close($stmt_user);
            }
            ?>
    </div>
</body>
</html>