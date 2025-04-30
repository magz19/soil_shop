<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set the user as admin
$_SESSION['is_admin'] = true;

echo "You are now an admin. <a href='../index.php?page=admin'>Go to Admin Dashboard</a>";
?>