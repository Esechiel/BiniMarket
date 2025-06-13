<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../php/db.php';

if (isset($_POST['email_con'], $_POST['pass_con'])) {
    $email = $_POST['email_con'];
    $pass = $_POST['pass_con'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if ($user = mysqli_fetch_assoc($res)) {
        if (password_verify($pass, $user['password_hash'])) {
            $_SESSION['user'] = $user;

            // Redirection selon le rôle
            if ($user['grade'] === 'super_admin') {
                header("Location: ../super_admin/dashboard.php");
            } elseif ($user['grade'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../profile.php");
            }
            exit;
        }
    }

    // En cas d'erreur d'identifiants
    header("Location: ../auth.php?error=Identifiants incorrects");
    exit;
}
?>
