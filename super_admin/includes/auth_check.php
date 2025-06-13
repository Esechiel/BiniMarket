<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['grade'])) {
    // Non connecté
    header('Location: ../../auth.php');
    exit();
}
?>