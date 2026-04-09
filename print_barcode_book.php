<?php 
// Start output buffering
ob_start();

include ('include/dbcon.php');

// Start the session only if it's not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('session.php'); // Your session validation script

// Check if the session variables are set before using them
if (!isset($_SESSION['book_title']) || !isset($_SESSION['book_pub'])) {
    die("Error: Book title or publisher not set in session. Please perform a search first.");
}
?>
<html>
<head>
    <title>Library Management System - Print Book Barcodes</title>
    <style>
        .container {
            width: 100%;
            margin: auto;
        }
        .table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse; /* Ensures borders are clean */
        }
        .table th, .table td {
            padding: 8px;
            border: 1px solid #ddd; /* Light gray border for table cells */
        }
        .table th {
            background-color: #f2f2f2; /* Light gray header */
            text-align: left;
        }
        .table-striped tbody > tr:nth-child(odd) > td,
        .table-striped tbody > tr:nth-child(odd) > th {
            background-color: #f9f9f9;
        }
        @media print {
            #print {
                display: none;
            }
        }
        #print {
            width: 90px;
            height: 30px;
            font-size: 18px;
            background: white;
            border-radius: 4px;
            margin-left: 28px;
            cursor: pointer;
            border: 1px solid #ccc;
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
        <hr style="border: solid black 1px">
        <button type="submit" id="print" onclick="printPage()">Print</button>
        <p style="margin-left:30px; margin-top:5px; margin-bottom: 0px;font-size:14pt; font-style: italic;">Book Barcode List</p>
        <div align="right">
            <b style="color:blue;">Date Prepared:</b>
            <?php echo date("l, d-m-Y"); ?>
        </div>
        <br/>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="text-align:center; width: 40%;">Barcode Image</th>
                    <th style="text-align:center;">Barcode</th>
                    <th style="text-align:center;">Title</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Use prepared statements for security
                    $book_title_param = $_SESSION['book_title'];
                    $book_pub_param = $_SESSION['book_pub'];
                    
                    $stmt = mysqli_prepare($con, "SELECT book_barcode, book_title FROM book WHERE book_title = ? AND book_pub = ? ORDER BY book_id DESC");
                    mysqli_stmt_bind_param($stmt, "ss", $book_title_param, $book_pub_param);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_array($result)) {
                ?>
                <tr>
                    <td style="text-align:center;">
                        <?php
                            // Safely encode the barcode for the URL
                            $barcode_safe_for_url = urlencode($row['book_barcode']);
                            // Point the image src to our new, modern generator script
                            echo '<img src="generate_barcode.php?code=' . $barcode_safe_for_url . '" alt="Barcode for ' . htmlspecialchars($row['book_barcode']) . '">';
                        ?>
                    </td>
                    <td style="text-align:center; vertical-align: middle;"><?php echo htmlspecialchars($row['book_barcode']); ?></td>
                    <td style="text-align:center; vertical-align: middle;"><?php echo htmlspecialchars($row['book_title']); ?></td>
                </tr>
                <?php
                        }
                    } else {
                        echo '<tr><td colspan="3" style="text-align:center;">No books found matching the criteria.</td></tr>';
                    }
                    mysqli_stmt_close($stmt);
                ?>
            </tbody>
        </table>
        <br />
        <br />
        <?php
            $user_query = mysqli_query($con, "SELECT firstname, lastname FROM admin WHERE admin_id='$id_session'") or die(mysqli_error($con));
            if ($row = mysqli_fetch_array($user_query)) {
        ?>
        <div style="margin-left: 28px;">
            <p><strong>Prepared by:</strong><br><?php echo htmlspecialchars($row['firstname'] . " " . $row['lastname']); ?></p>
        </div>
        <?php } ?>
    </div>
</body>
</html>
<?php
// Flush the output buffer
ob_end_flush();
?>