<?php
session_start();
require_once 'db.php';
$user_id = $_SESSION['user']['user_id'];
mysqli_query($conn, "UPDATE users SET is_active = 0, last_login = NOW() WHERE user_id = $user_id");
session_destroy();
header("Location: ../index.php");
exit;
?>