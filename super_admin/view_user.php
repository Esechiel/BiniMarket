<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['grade'] !== 'super_admin') {
    // Redirection si non connecté ou mauvais rôle
    header("Location: ../auth.php?error=Accès réservé au super administrateur");
    exit;
}


$host = 'localhost';
$dbname = 'binimarket';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $dbname);

// Vérification
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}


if (!isset($_GET['id'])) {
    die("ID utilisateur non spécifié.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Utilisateur non trouvé.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil utilisateur</title>
    <link rel="stylesheet" href="../styles/admin.css"> <!-- Inclut le style global -->
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #0b1c3b;
        color: white;
    }

    .main-content {
        margin-left: 250px; /* Adapter si ton sidebar a une autre largeur */
        padding: 30px;
        min-height: 100vh;
        box-sizing: border-box;
    }

    .profile-box {
        background-color: #12264d;
        padding: 30px;
        border-radius: 12px;
        max-width: 700px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        margin: 0 auto;
    }

    .profile-box h2 {
        margin-bottom: 20px;
        border-bottom: 2px solid #1e4b8b;
        padding-bottom: 10px;
        font-size: 24px;
    }

    .profile-box p {
        margin: 12px 0;
        font-size: 16px;
    }

    .profile-box img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px;
    }
</style>

</head>
<body>
<?php include 'includes/sidebar_sa.php'; ?>

<div class="main-content">
    <div class="profile-box">
        <h2>Profil de <?= htmlspecialchars($user['username']) ?></h2>
        <img src="../<?= $user['profile_picture'] ?>" alt="Photo de profil"><br><br>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['phone_number']) ?></p>
        <p><strong>Bio :</strong> <?= htmlspecialchars($user['bio']) ?></p>
        <p><strong>Grade :</strong> <?= htmlspecialchars($user['grade']) ?></p>
        <p><strong>Inscrit le :</strong> <?= $user['registration_date'] ?></p>
        <p><strong>Dernière connexion :</strong> <?= $user['last_login'] ?: 'Jamais connecté' ?></p>
    </div>
</div>

</body>
</html>
