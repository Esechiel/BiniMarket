<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../php/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['grade'] !== 'super_admin') {
    header("Location: ../auth.php?msg=Accès refusé");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $parent_id = !empty($_POST['parent_category_id']) ? intval($_POST['parent_category_id']) : "NULL";

    $upload_dir = "../images/categories/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $icon1_path = '';
    $icon2_path = '';

    // Upload icon1 (obligatoire)
    if (isset($_FILES['icon1']) && $_FILES['icon1']['error'] === 0) {
        $ext = pathinfo($_FILES['icon1']['name'], PATHINFO_EXTENSION);
        $filename = uniqid("icon1_") . "." . $ext;
        $target = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['icon1']['tmp_name'], $target)) {
            $icon1_path = mysqli_real_escape_string($conn, $filename);
        }
    }

    // Upload icon2 (facultatif)
    if (isset($_FILES['icon2']) && $_FILES['icon2']['error'] === 0) {
        $ext = pathinfo($_FILES['icon2']['name'], PATHINFO_EXTENSION);
        $filename = uniqid("icon2_") . "." . $ext;
        $target = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['icon2']['tmp_name'], $target)) {
            $icon2_path = mysqli_real_escape_string($conn, $filename);
        }
    }

    // Insertion dans la base de données
    $query = "INSERT INTO categories (name, description, icon1, icon2, parent_category_id)
              VALUES ('$name', '$description', '$icon1_path', '$icon2_path', $parent_id)";

    if (mysqli_query($conn, $query)) {
        header("Location: ../categories.php?success=1");
        exit;
    } else {
        header("Location: ../add_category.php?error=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une catégorie - BiniMarket</title>
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            margin: 60px auto;
            width: 50%;
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #3498db;
        }
        form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        form input, form textarea, form select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }
        .btn-submit {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            margin-top: 20px;
            width: 100%;
            font-size: 16px;
        }
        .btn-submit:hover {
            background-color: #293c5d;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        a.back {
            display: inline-block;
            margin-top: 15px;
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ajouter une catégorie</h2>
        <?php if (!empty($message)) echo "<p class='error'>$message</p>"; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="name">Nom de la catégorie *</label>
            <input type="text" name="name" id="name" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4"></textarea>

            <!-- Icône 1 (upload image) -->
            <div class="form-group">
                <label for="icon1">Icône principale (image .png, .jpg, .svg)</label>
                <input type="file" id="icon1" name="icon1" accept=".png,.jpg,.jpeg,.svg" required>
            </div>

            <!-- Icône 2 (facultative) -->
            <div class="form-group">
                <label for="icon2">Icône secondaire (facultatif)</label>
                <input type="file" id="icon2" name="icon2" accept=".png,.jpg,.jpeg,.svg">
            </div>



            <label for="parent_category_id">Catégorie parente (facultatif)</label>
            <select name="parent_category_id" id="parent_category_id">
                <option value="">Aucune</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-submit">Ajouter</button>
        </form>
        <a href="categories.php" class="back">← Retour à la liste des catégories</a>
    </div>
</body>
</html>
