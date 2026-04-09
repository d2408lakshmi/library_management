<?php
// Include necessary files first
include ('include/dbcon.php');
include ('header.php');

// --- FORM PROCESSING LOGIC ---
// Check if the form was submitted using the POST method and the 'submit' button was clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    // Check if an image was uploaded
    if (isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        
        $file = $_FILES['image']['tmp_name'];
        $image = $_FILES["image"]["name"];
        $size = $_FILES["image"]["size"];
        
        // Check for file size
        if ($size > 10000000) { // 10MB
            die("Error: File size is too big!");
        }

        // Move the uploaded file
        $upload_path = "upload/" . basename($image);
        if (move_uploaded_file($file, $upload_path)) {
            $book_image = $image;

            // Get all data from the form POST
            $book_title = $_POST['book_title'];
            $category = $_POST['category'];
            $author = $_POST['author'];
            $author_2 = $_POST['author_2'];
            $author_3 = $_POST['author_3'];
            $author_4 = $_POST['author_4'];
            $author_5 = $_POST['author_5'];
            $book_pub = $_POST['book_pub'];
            $publisher_name = $_POST['publisher_name'];
            $isbn = $_POST['isbn'];
            $copyright_year = $_POST['copyright_year'];
            $status = $_POST['status'];
            $n = (int)$_POST['book_copies']; // Cast to integer for safety

            // Loop to insert the specified number of copies
            for ($i = 1; $i <= $n; $i++) {
                // --- Generate New Barcode ---
                $query = mysqli_query($con, "SELECT mid_barcode FROM `barcode` ORDER BY mid_barcode DESC LIMIT 1") or die(mysqli_error($con));
                
                $mid_barcode = 0; // Default if table is empty
                if ($fetch = mysqli_fetch_array($query)) {
                    $mid_barcode = $fetch['mid_barcode'];
                }

                $new_barcode_mid = $mid_barcode + 1;
                $pre = "KIT";
                $suf = "VNS";
                $gen = $pre . $new_barcode_mid . $suf;

                $remark = ($status == 'Lost') ? 'Not Available' : 'Available';

                // --- Use Prepared Statements for Security ---
                $stmt_book = mysqli_prepare($con, "INSERT INTO book (book_title, category, author, author_2, author_3, author_4, author_5, book_pub, publisher_name, isbn, copyright_year, status, book_barcode, book_image, date_added, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
                mysqli_stmt_bind_param($stmt_book, "sssssssssssssss", $book_title, $category, $author, $author_2, $author_3, $author_4, $author_5, $book_pub, $publisher_name, $isbn, $copyright_year, $status, $gen, $book_image, $remark);
                mysqli_stmt_execute($stmt_book) or die(mysqli_error($con));
                
                $stmt_barcode = mysqli_prepare($con, "INSERT INTO barcode (pre_barcode, mid_barcode, suf_barcode) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt_barcode, "sis", $pre, $new_barcode_mid, $suf);
                mysqli_stmt_execute($stmt_barcode) or die(mysqli_error($con));
            }

            // Redirect after successful processing
            header('location: view_all_barcode.php?loop=' . $n);
            exit(); // Always exit after a header redirect

        } else {
            echo "<div class='alert alert-danger'>Error: Could not move uploaded file. Check permissions.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error: No image was uploaded or there was an upload error.</div>";
    }
}
?>

<!-- === HTML FORM SECTION === -->
<div class="page-title">
    <div class="title_left">
        <h3>
            <small>Home / Books /</small> Add Book
        </h3>
    </div>
</div>
<div class="clearfix"></div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-plus"></i> Add Book</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <!-- content starts here -->

                <form method="post" enctype="multipart/form-data" class="form-horizontal form-label-left">
                    <!-- The unnecessary hidden input has been REMOVED -->
                    
                    <div class="form-group">
                        <label class="control-label col-md-4" for="book_title">Title <span class="required" style="color:red;">*</span></label>
                        <div class="col-md-4">
                            <input type="text" name="book_title" id="book_title" required="required" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="author">Author 1 <span class="required" style="color:red;">*</span></label>
                        <div class="col-md-4">
                            <input type="text" name="author" id="author" required="required" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="author_2">Author 2</label>
                        <div class="col-md-4">
                            <input type="text" name="author_2" id="author_2" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="author_3">Author 3</label>
                        <div class="col-md-4">
                            <input type="text" name="author_3" id="author_3" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="author_4">Author 4</label>
                        <div class="col-md-4">
                            <input type="text" name="author_4" id="author_4" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="author_5">Author 5</label>
                        <div class="col-md-4">
                            <input type="text" name="author_5" id="author_5" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="book_pub">Publication <span class="required" style="color:red;">*</span></label>
                        <div class="col-md-4">
                            <input type="text" name="book_pub" id="book_pub" class="form-control" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="publisher_name">Publisher</label>
                        <div class="col-md-4">
                            <input type="text" name="publisher_name" id="publisher_name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="isbn">ISBN <span class="required" style="color:red;">*</span></label>
                        <div class="col-md-4">
                            <input type="text" name="isbn" id="isbn" class="form-control" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="copyright_year">Copyright &copy;</label>
                        <div class="col-md-3">
                            <input type="number" name="copyright_year" id="copyright_year" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="book_copies">Copies <span class="required" style="color:red;">*</span></label>
                        <div class="col-md-3">
                            <input type="number" name="book_copies" step="1" min="1" max="1000" required="required" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="status">Status <span class="required" style="color:red;">*</span></label>
                        <div class="col-md-3">
                            <select name="status" id="status" class="select2_single form-control" tabindex="-1" required="required">
                                <option value="New">New</option>
                                <option value="Lost">Lost</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="category">Category <span class="required" style="color:red;">*</span></label>
                        <div class="col-md-3">
                            <select name="category" id="category" class="select2_single form-control" tabindex="-1" required="required">
                                <option value="CSE">CSE</option>
                                <option value="ME">ME</option>
                                <option value="EC">EC</option>
                                <option value="EN">EN</option>
                                <option value="Civil">Civil</option>
                                <option value="BBA">BBA</option>
                                <option value="MBA">MBA</option>
                                <option value="B-Pharma">B-Pharma</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="image">Book Image</label>
                        <div class="col-md-4">
                            <input type="file" style="height:44px;" name="image" id="image" class="form-control">
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                            <a href="book.php" class="btn btn-primary"><i class="fa fa-times-circle-o"></i> Cancel</a>
                            <button type="submit" name="submit" class="btn btn-success"><i class="fa fa-plus-square"></i> Submit</button>
                            <button type="button" id="btn_autofill" class="btn btn-info pull-right"><i class="fa fa-magic"></i> Autofill from Cover</button>
                        </div>
                    </div>
                </form>
                <!-- content ends here -->
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btn_autofill').addEventListener('click', function() {
    var fileInput = document.getElementById('image');
    if(fileInput.files.length === 0) {
        alert('Please select a Book Image to scan first!');
        return;
    }
    
    var file = fileInput.files[0];
    var formData = new FormData();
    formData.append("image", file);
    
    var btn = this;
    var originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Scanning...';
    btn.disabled = true;

    fetch('http://127.0.0.1:5000/api/scan-book', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.title) document.getElementById('book_title').value = data.title;
        if(data.author) document.getElementById('author').value = data.author;
        if(data.publisher_name) document.getElementById('publisher_name').value = data.publisher_name;
        // Optionally map other fields if returned
        alert("Autofill complete! Please review the extracted data.");
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to scan image. Ensure the AI microservice is running.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});
</script>
            </div>
        </div>
    </div>
</div>

<?php include ('footer.php'); ?>