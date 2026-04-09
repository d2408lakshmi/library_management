<!-- sidebar navigation -->
<div class="col-md-3 left_col">
    <div class="left_col scroll-view">

        <div class="navbar nav_title" style="border: 0;">
            <a href="home.php" class="site_title"><i class="fa fa-university"></i> <span>Library Pro </span></a>
        </div>
        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <?php
            include_once('include/dbcon.php'); // Use include_once to prevent re-declarations
            
            $user_query = mysqli_query($con, "SELECT * FROM admin WHERE admin_id='$id_session'") or die(mysqli_error($con));
            $row = mysqli_fetch_array($user_query);
            // Normalize the admin type to lowercase for robust checking
            $admin_type = strtolower($row['admin_type']); 
        ?>
        <!-- Profile section is restored to fix the layout -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <?php if(!empty($row['admin_image'])): ?>
                    <img src="upload/<?php echo $row['admin_image']; ?>" alt="..." class="img-circle profile_img">
                <?php else: ?>
                    <img src="images/user.png" alt="..." class="img-circle profile_img">
                <?php endif; ?>
            </div>
            <div class="profile_info">
                <span>Welcome,</span>
                <!-- Using htmlspecialchars to prevent XSS attacks -->
                <h2><?php echo htmlspecialchars($row['firstname']); ?></h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br />

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

            <div class="menu_section">
                <!-- The inline style causing the large gap has been REMOVED -->
                <h3>File Information</h3>
                <div class="separator"></div>
                <ul class="nav side-menu">
                    <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
                    <li><a href="member.php"><i class="fa fa-users"></i> Members</a></li>
                    
                    <?php if($admin_type == 'admin') { ?>
                    <li><a href="admin.php"><i class="fa fa-user"></i> Admin / Librarian</a></li>
                    <?php } ?>
                    
                    <li><a href="book.php"><i class="fa fa-book"></i> Books</a></li>

                    <?php if($admin_type == 'librarian') { ?>
                    <li><a href="librarian.php"><i class="fa fa-user"></i> Librarian</a></li>
                    <?php } ?>

                    <?php if($admin_type == 'admin') { ?>
                    <li><a href="user_log_in.php"><i class="fa fa-history"></i> Members Record</a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="menu_section">
                <h3>Transaction Information</h3>
                <div class="separator"></div>
                <ul class="nav side-menu">
                    <li><a href="borrow.php"><i class="fa fa-edit"></i> Borrow / Return</a></li>
                    <li><a href="report.php"><i class="fa fa-file"></i> Reports</a></li>
                    <li><a href="individual_report.php"><i class="fa fa-file"></i> Individual Report</a></li>
                    <li><a href="borrowed_book.php"><i class="fa fa-book"></i> Borrowed books</a></li>
                    <li><a href="returned_book.php"><i class="fa fa-book"></i> Returned books</a></li>

                    <?php if($admin_type == 'admin') { ?>
                    <li><a href="settings.php"><i class="fa fa-cog"></i> Settings</a></li>
                    <?php } ?>

                    <li><a href="about_us.php"><i class="fa fa-info"></i> About Us</a></li>
                </ul>
            </div>
        </div>
        <!-- /sidebar menu -->
    </div>
</div>
<!-- end of sidebar navigation -->