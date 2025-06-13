<?php 
    require 'db.php';
    session_start();
if (isset($_POST['submit_review'])) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $reviewer_id = $_SESSION['user']['user_id'];
    $reviewed_id = $_SESSION['reviewed_user_id'];

    // Sécurité : éviter l'injection SQL
    $stmt = $conn->prepare("INSERT INTO reviews (reviewer_id, reviewed_user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $reviewer_id, $reviewed_id, $rating, $comment);
    
    if ($stmt->execute()) {
        echo "<p>Avis enregistré avec succès !</p>";
        // Optionnel : rediriger pour éviter la double soumission
        header("Location: ../profile.php?id=$reviewed_id");
        exit;
    } else {
        echo "<p>Erreur lors de l'enregistrement de l'avis : " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>
