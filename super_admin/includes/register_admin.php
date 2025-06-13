<?php
session_start();
require_once '../../../php/db.php';

if (isset($_POST['pseudo'],$_POST['location'], $_POST['email'], $_POST['pass'],$_POST['phone'],$_POST['choixpolitic'])) {
    $username = mysqli_real_escape_string($conn, $_POST['pseudo']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $location = $_POST['location'];
    $phone = $_POST['phone'];
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    $sql1 = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($conn, $sql1);
    if (mysqli_num_rows($res) > 0) {
        header("Location: ../add_admin.php?error=User existe deja");
        exit;
    }

    $_SESSION['email'] = $email;
    $sql2 = "INSERT INTO users (username, email, password_hash, phone_number, location_id, last_login, grade) VALUES ('$username', '$email', '$pass', '$phone','$location', 'NOW()', 'admin')";
    if (mysqli_query($conn, $sql2)) {
        header("Location: ../dashboard.php?success=Inscription réussie");
    } else {
        header("Location: ../add_admin.php?error=Erreur lors de l'inscription");
    }
}
?>
