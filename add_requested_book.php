<?php
include('include/dbcon.php');

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $book_pub = isset($_POST['publisher']) ? trim($_POST['publisher']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'CSE';

    if (empty($book_title) || empty($author)) {
        echo json_encode(["status" => "error", "message" => "Title and Author are required."]);
        exit();
    }

    // --- Generate New Barcode ---
    $query = mysqli_query($con, "SELECT mid_barcode FROM `barcode` ORDER BY mid_barcode DESC LIMIT 1");
    $mid_barcode = 0; // Default if table is empty
    if ($query && $fetch = mysqli_fetch_array($query)) {
        $mid_barcode = (int)$fetch['mid_barcode'];
    }

    $new_barcode_mid = $mid_barcode + 1;
    $pre = "KIT";
    $suf = "VNS";
    $gen = $pre . $new_barcode_mid . $suf;

    $status = 'New';
    $remark = 'Available';
    $book_image = ''; 
    $author_2 = '';
    $author_3 = '';
    $author_4 = '';
    $author_5 = '';
    $publisher_name = $book_pub;
    $isbn = 'AUTO-' . rand(100000, 999999);
    $copyright_year = (int)date('Y');

    // --- Insert book into database ---
    $stmt_book = mysqli_prepare($con, "INSERT INTO book (book_title, category, author, author_2, author_3, author_4, author_5, book_pub, publisher_name, isbn, copyright_year, status, book_barcode, book_image, date_added, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    mysqli_stmt_bind_param($stmt_book, "sssssssssssssss", $book_title, $category, $author, $author_2, $author_3, $author_4, $author_5, $book_pub, $publisher_name, $isbn, $copyright_year, $status, $gen, $book_image, $remark);
    
    if (mysqli_stmt_execute($stmt_book)) {
        // --- Record used barcode mid ---
        $stmt_barcode = mysqli_prepare($con, "INSERT INTO barcode (pre_barcode, mid_barcode, suf_barcode) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_barcode, "sis", $pre, $new_barcode_mid, $suf);
        mysqli_stmt_execute($stmt_barcode);

        echo json_encode(["status" => "success", "message" => "Book requested and successfully added to the catalog!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database insertion failed: " . mysqli_error($con)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
