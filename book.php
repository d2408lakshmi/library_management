<?php include ('header.php'); ?>

        <div class="page-title">
            <div class="title_left">
                <h3>
					<small>Home /</small> Books
                </h3>
            </div>
        </div>
        <div class="clearfix"></div>
 
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2><i class="fa fa-search"></i> Search Specific Books</h2>
                         <ul class="nav navbar-right panel_toolbox">
                            <li>
                                
                                <a href="add_book.php" style="background:none;">
                                <button class="btn btn-primary"><i class="fa fa-plus"></i> Add Book</button>
                                </a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <!-- Search Form remains here -->
                        <form method="post" action="book_search.php" class="form-inline">
                            <div class="form-group">
                                <select name="book_title" class="select2_single form-control" tabindex="-1" >
                                    <option value="0">Select Title</option>
                                    <?php
                                    $result_titles = mysqli_query($con, "SELECT DISTINCT book_title FROM book ORDER BY book_title ASC") or die (mysqli_error($con));
                                    while ($row_title = mysqli_fetch_array($result_titles)) {
                                        echo '<option value="' . htmlspecialchars($row_title['book_title']) . '">' . htmlspecialchars($row_title['book_title']) . '</option>';
                                    }
                                    ?>
                                </select>  
                            </div>
                            <div class="form-group">
                                 <select name="book_pub" class="select2_single form-control" tabindex="-1" >
                                    <option value="0">Select Publisher</option>
                                    <?php
                                    $result_pubs = mysqli_query($con, "SELECT DISTINCT book_pub FROM book ORDER BY book_pub ASC") or die (mysqli_error($con));
                                    while ($row_pub = mysqli_fetch_array($result_pubs)) {
                                        echo '<option value="' . htmlspecialchars($row_pub['book_pub']) . '">' . htmlspecialchars($row_pub['book_pub']) . '</option>';
                                    }
                                    ?>
                                </select>  
                            </div>
                            <button name="submit" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        </form>
                        <div class="ln_solid"></div>
                        <!-- End of Search Form -->
                    </div>
                </div>

                <div class="x_panel">
                    <div class="x_title">
                        <h2><i class="fa fa-book"></i> All Books</h2>
                        <ul class="nav navbar-right panel_toolbox">
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                        <ul class="nav nav-pills">
                            <li role="presentation" class="active"><a href="book.php">All Books</a></li>
                            <li role="presentation"><a href="lost_books.php">Lost Books</a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <!-- content starts here -->
						<div class="table-responsive">
							<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="all_books_table">
								
							<thead>
								<tr>
									<th style="width:100px;">Image</th>
									<th>Barcode</th>
									<th>Title</th>
									<th>Author(s)</th>
									<th>Category</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							
							<?php
							$result = mysqli_query($con, "SELECT * FROM book ORDER BY book_id DESC") or die(mysqli_error($con));
							while ($row = mysqli_fetch_array($result)) {
    							$id = $row['book_id'];
							?>
							<tr>
								<td>
								<?php if($row['book_image'] != ""): ?>
								<img src="upload/<?php echo htmlspecialchars($row['book_image']); ?>" class="img-thumbnail" width="75px" height="50px">
								<?php else: ?>
								<img src="images/book_image.jpg" class="img-thumbnail" width="75px" height="50px">
								<?php endif; ?>
								</td>
								<td><a target="_blank" href="print_barcode_individual.php?code=<?php echo urlencode($row['book_barcode']); ?>"><?php echo htmlspecialchars($row['book_barcode']); ?></a></td>
								<td style="word-wrap: break-word; width: 20em;"><?php echo htmlspecialchars($row['book_title']); ?></td>
								<td style="word-wrap: break-word; width: 15em;"><?php echo htmlspecialchars($row['author']); ?></td>
								<td><?php echo htmlspecialchars($row['category']); ?></td> 
								<td><?php echo htmlspecialchars($row['status']); ?></td> 
								<td>
									<a class="btn btn-primary btn-xs" for="ViewAdmin" href="view_book.php?book_id=<?php echo $id; ?>">
										<i class="fa fa-search"></i> View
									</a>
									<a class="btn btn-warning btn-xs" for="ViewAdmin" href="edit_book.php?book_id=<?php echo $id; ?>">
									<i class="fa fa-edit"></i> Edit
									</a>
									<a class="btn btn-danger btn-xs" for="DeleteAdmin" href="#delete<?php echo $id;?>" data-toggle="modal" data-target="#delete<?php echo $id;?>">
										<i class="glyphicon glyphicon-trash icon-white"></i> Del
									</a>
			
									<!-- delete modal user -->
									<div class="modal fade" id="delete<?php echo $id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-user"></i> Delete Book</h4>
										</div>
										<div class="modal-body">
												<div class="alert alert-danger">
													Are you sure you want to delete this book?
												</div>
										</div>
										<div class="modal-footer">
												<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove icon-white"></i> No</button>
												<a href="delete_book.php?book_id=<?php echo $id; ?>" style="margin-bottom:5px;" class="btn btn-primary"><i class="glyphicon glyphicon-ok icon-white"></i> Yes</a>
										</div>
										</div>
									</div>
									</div>
								</td> 
							</tr>
							<?php } ?>
							</tbody>
							</table>
						</div>
                        <!-- content ends here -->
                    </div>
                </div>
            </div>
        </div>

<!-- Add this script at the end of the file -->
<script>
$(document).ready(function() {
    $('#all_books_table').DataTable({
        "paging": true,       // Enable pagination
        "searching": true,    // Enable the search box
        "ordering": true,     // Enable column sorting
        "info": true,         // Show "Showing 1 to X of Y entries"
        "responsive": true    // Make the table responsive
    });
});
</script>

<?php include ('footer.php'); ?>