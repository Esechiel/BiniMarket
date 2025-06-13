<?php
session_start();
require_once 'db.php';

if (isset($_POST['email_con'], $_POST['pass_con'])) {
    $email = $_POST['email_con'];
    $pass = $_POST['pass_con'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if ($user = mysqli_fetch_assoc($res)) {
        if (password_verify($pass, $user['password_hash'])) {
            $_SESSION['user'] = $user;
            if ($user['grade'] === 'admin' || $user['grade'] === 'super_admin') {
                header("Location: ../admin/choix-role.php");
            } else {
                header("Location: ../profile.php");
            }
            exit;
        }
    }
    header("Location: ../auth.php?error=Identifiants incorrects");
}
?>