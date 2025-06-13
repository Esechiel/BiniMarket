<?php
session_start();

// Vérification du rôle super_admin
if (!isset($_SESSION['user']) || $_SESSION['user']['grade'] !== 'super_admin') {
    header("Location: ../auth.html?error=Accès réservé au super administrateur");
    exit;
}

require_once '../php/db.php';

// Récupérer tous les noms de catégories avec leurs IDs
$parentNames = [];
$parentResult = $conn->query("SELECT category_id, name FROM categories");
while ($row = $parentResult->fetch_assoc()) {
    $parentNames[$row['category_id']] = $row['name'];
}


// Récupération des catégories
$sql = "SELECT * FROM categories ORDER BY category_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des catégories</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            background-color: #f4f6f9;
            min-height: 100vh;
        }

        .card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 30px;
        }

        h2 {
            color: #1a237e;
            margin-bottom: 10px;
        }

        .btn-add {
            background-color: #1a237e;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            float: right;
            margin-bottom: 15px;
        }

        .btn-add:hover {
            background-color: #0d173d;
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
            background-color: #1a237e;
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
    </style>
</head>
<body>

<?php include 'includes/sidebar_sa.php'; ?>

<div class="main-content">
    <div class="card">
        <h2>Liste des catégories</h2>
        <a href="add_category.php" class="btn-add">+ Ajouter une catégorie</a>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Icône 1</th>
                    <th>Icône 2</th>
                    <th>Catégorie parent</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['category_id']; ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['description']); ?></td>
                            <td><img src="../<?= htmlspecialchars($row['icon1']); ?>" alt="Icon 1" class="icon-preview"></td>
                            <td><img src="../<?= htmlspecialchars($row['icon2']); ?>" alt="Icon 2" class="icon-preview"></td>
                            <td>
                                <?= isset($row['parent_category_id']) && isset($parentNames[$row['parent_category_id']]) 
                                    ? htmlspecialchars($parentNames[$row['parent_category_id']]) 
                                    : 'Aucune'; ?>
                            </td>

                            <td>
                                <a href="delete_category.php?id=<?= $row['category_id']; ?>" class="btn-delete" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">Aucune catégorie trouvée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
