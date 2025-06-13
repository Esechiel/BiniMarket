<?php
session_start();
require 'db.php';
require_once 'add_notification.php';

if (isset($_SESSION['user']['user_id'])) {
    $user_id = $_SESSION['user']['user_id'];
    $listing_id = $_POST['listing_id'];

    // Vérifie si l'annonce est déjà dans les favoris
    $stmt = $conn->prepare("SELECT favorite_id FROM favorites WHERE user_id = ? AND listing_id = ?");
    $stmt->bind_param("ii", $user_id, $listing_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si l'annonce est déjà dans les favoris, on la supprime
    if ($result->num_rows > 0) {
        // Récupérer le propriétaire de l’annonce
        $sql = "SELECT user_id, title FROM listings WHERE listing_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $listing_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            $owner_id = $row['user_id'];
            $title = $row['title'];
            addNotification($conn, $owner_id, 'favori', "Votre annonce \"$title\" a été retirée aux favoris.", $listing_id);
        }

        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND listing_id = ?");
        $stmt->bind_param("ii", $user_id, $listing_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>
                    alert('Annonce supprimée des favoris.');
                    window.location.href = '../search.php';
                </script>";

        exit;
        
    } else {
        // Récupérer le propriétaire de l’annonce
        $sql = "SELECT vendeur_id, title FROM listings WHERE listing_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $listing_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            $owner_id = $row['vendeur_id'];
            $title = $row['title'];
            addNotification($conn, $owner_id, 'favori', "Votre annonce \"$title\" a été ajoutée aux favoris.", $listing_id);
        }

        // Si l'annonce n'est pas dans les favoris, on l'ajoute
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, listing_id, date_added) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $listing_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>
                    alert('Annonce ajoutée aux favoris.');
                    window.location.href = '../search.php';
            </script>";
                  exit;
    }

}else{
    echo "<script>
                    alert('Veuillez vous connecter');
                    window.location.href = '../auth.php';
            </script>";
                  exit;
}
?>

