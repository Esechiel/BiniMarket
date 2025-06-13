<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['grade'] !== 'super_admin') {
    // Redirection si non connecté ou mauvais rôle
    header("Location: ../auth.php?error=Accès réservé au super administrateur");
    exit;
}



require_once '../php/db.php';

// Traitement des actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'block') {
        $stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Erreur préparation BLOCK : " . $conn->error);
        }
    } elseif ($action === 'unblock') {
        $stmt = $conn->prepare("UPDATE users SET is_active = 1 WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Erreur préparation UNBLOCK : " . $conn->error);
        }
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Erreur préparation DELETE : " . $conn->error);
        }
    }

    header("Location: users.php");
    exit;
}

// Recherche et filtres
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT * FROM users WHERE 1";
$params = [];
$types = "";

// Recherche globale
if (!empty($search)) {
    $sql .= " AND (username LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
    $types .= "sss";
    $search_term = "%" . $search . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

// Filtre par rôle
if (!empty($role_filter)) {
    $sql .= " AND grade = ?";
    $types .= "s";
    $params[] = $role_filter;
}

// Filtre par statut
if (!empty($status_filter)) {
    $sql .= " AND is_active = ?";
    $types .= "i";
    $params[] = $status_filter === 'actif' ? 1 : 0;
}

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    die("Erreur préparation REQUETE LISTE : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs - Admin</title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>

<?php include 'includes/sidebar_sa.php'; ?>

<div class="main-content">
    <h1>Gestion des utilisateurs</h1>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Recherche par nom, email ou téléphone" value="<?= htmlspecialchars($search) ?>">
        <select name="role">
            <option value="">-- Rôle --</option>
            <option value="user" <?= $role_filter === 'user' ? 'selected' : '' ?>>Utilisateur</option>
            <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            <option value="super_admin" <?= $role_filter === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
        </select>
        <select name="status">
            <option value="">-- Statut --</option>
            <option value="actif" <?= $status_filter === 'actif' ? 'selected' : '' ?>>Actif</option>
            <option value="bloqué" <?= $status_filter === 'bloqué' ? 'selected' : '' ?>>Bloqué</option>
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pseudo</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['user_id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone_number']) ?></td>
                <td><?= $user['grade'] ?></td>
                <td><?= $user['is_active'] ? 'Actif' : 'Bloqué' ?></td>
                <td>
                    <?php if (!in_array($user['grade'], ['admin', 'super_admin'])): ?>
                        <?php if ($user['is_active']): ?>
                            <a href="?action=block&id=<?= $user['user_id'] ?>" class="btn btn-block">Bloquer</a>
                        <?php else: ?>
                            <a href="?action=unblock&id=<?= $user['user_id'] ?>" class="btn btn-unblock">Débloquer</a>
                        <?php endif; ?>
                        <a href="?action=delete&id=<?= $user['user_id'] ?>" class="btn btn-delete" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                    <?php endif; ?>
                    <a href="view_user.php?id=<?= $user['user_id'] ?>" class="btn btn-view">Voir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
  

.sidebar {
    width: 250px;
    background-color: #3498db;
    min-height: 100vh;
    padding-top: 20px;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
}

.main-content {
    margin-left: 250px;
    padding: 40px;
    flex-grow: 1;
    background-color: #f9f9f9;
}

h1 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #3498db;
    border-left: 5px solid #3498db;
    padding-left: 10px;
}

.search-form {
    margin-bottom: 30px;
}

.search-form input {
    padding: 10px 15px;
    width: 300px;
    border-radius: 5px;
    border: none;
    background-color: #3498db;
    color: white;
    margin-right: 10px;
}

.search-form button {
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.search-form button:hover {
    background-color: #3498db;
}

.search-form select {
    padding: 8px;
    margin-left: 10px;
    border-radius: 5px;
    border: none;
}


.user-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #f9f9f9;
    border-radius: 8px;
    overflow: hidden;
}

.user-table th, .user-table td {
    padding: 14px 12px;
    border-bottom: 1px solid #3498db;
    text-align: left;
}

.user-table th {
    background-color: #3498db;
    color: #ffffff;
    font-weight: bold;
}

.user-table tr:hover {
    background-color: #f9f9f9;
}

.btn {
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    margin-right: 5px;
    color: white;
    font-size: 14px;
    display: inline-block;
}

.btn-block { background-color: #f39c12; }
.btn-unblock { background-color: #2ecc71; }
.btn-delete { background-color: #e74c3c; }
.btn-view { background-color: #3498db; }

.btn:hover {
    opacity: 0.85;
}


</style>

</body>
</html>
