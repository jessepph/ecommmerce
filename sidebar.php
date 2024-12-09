<?php
//session_start();
require('db.php');
// Assuming $conn is the database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get message counts for the logged-in user
$fromUser = $_SESSION['username'];

// Inbox count
$inbox_sql = "SELECT COUNT(*) AS count FROM pm_inbox WHERE username = '$fromUser' AND viewed = 0";
$inbox_result = mysqli_query($con, $inbox_sql);
$inbox_count = mysqli_fetch_assoc($inbox_result)['count'];

// Sent items count
$sent_sql = "SELECT COUNT(*) AS count FROM pm_outbox WHERE username = '$fromUser'";
$sent_result = mysqli_query($con, $sent_sql);
$sent_count = mysqli_fetch_assoc($sent_result)['count'];
?>
<!-- Sidebar code -->
<div class="navigation">
    <div class="wrapper">
        <ul>
            <!-- Other menu items -->
            <li class="dropdown-link dropdown-large">
                <a href="messages.php" class="dropbtn">
                    Messages&nbsp;
                    <span class="badge badge-secondary"><?php echo $inbox_count; ?></span>
                </a>
                <div class="dropdown-content right-dropdown">
                    <a href="compose.php?action=compose">Compose Message</a>
                    <a href="messages.php">Inbox <span class="badge badge-secondary"><?php echo $inbox_count; ?></span></a>
                    <a href="messages.php?action=sent">Sent Items <span class="badge badge-secondary"><?php echo $sent_count; ?></span></a>
                </div>
            </li>
            <!-- Other menu items -->
        </ul>
    </div>
</div>
