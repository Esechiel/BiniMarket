<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['grade'] !== 'super_admin') {
    // Redirection si non connecté ou mauvais rôle
    header("Location: ../auth.php?error=Accès réservé au super administrateur");
    exit;
}


require_once '../php/db.php';



// Suppression d’un administrateur
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND grade = 'admin'");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admins.php");
    exit();
}


$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$query = "SELECT * FROM users WHERE grade = 'admin'";

if (!empty($search)) {
    $query .= " AND (username LIKE ? OR email LIKE ?)";
    $stmt = $conn->prepare($query);
    $like = '%' . $search . '%';
    $stmt->bind_param("ss", $like, $like);
} else {
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'includes/sidebar_sa.php'; ?>
<div class="main-content">
  <h1>Gestion des administrateurs</h1>
  <a href="add_admin.php" class="btn btn-add">+ Ajouter un administrateur</a>
  <form method="get" class="search-bar">
    <input type="text" name="q" placeholder="Rechercher par nom ou email..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
    <button type="submit">Rechercher</button>
</form>

  <table>
    <thead>
      <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($admin = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($admin['username']) ?></td>
        <td><?= htmlspecialchars($admin['email']) ?></td>
        <td><?= htmlspecialchars($admin['phone_number']) ?></td>
        <td>
          <a href="admins.php?delete=<?= $admin['user_id'] ?>" class="btn btn-delete" onclick="return confirm('Supprimer cet administrateur ?')">Supprimer</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<style>
.main-content {
  margin-left: 240px;
  padding: 20px;
  font-family: 'Segoe UI', sans-serif;
}

.search-bar {
  margin-bottom: 20px;
  display: flex;
  gap: 10px;
  max-width: 400px;
}

.search-bar input {
  flex: 1;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.search-bar button {
  padding: 8px 15px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.btn-add {
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            float: right;
            margin-bottom: 10px;
        }

        .btn-add:hover {
            background-color: #293c5d;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e5e5;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        .icon-preview {
            height: 40px;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        h2 {
            color: #3498db;
        }
</style>
