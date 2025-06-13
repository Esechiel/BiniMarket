<?php
session_start();
require_once 'php/db.php';

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user']['user_id'];
$message = '';
$transaction = null;

// On récupère la dernière transaction effectuée par cet utilisateur
$stmt = $pdo->prepare("
    SELECT t.*, l.title AS listing_title, u.username AS vendeur_nom
    FROM transactions t
    JOIN listings l ON t.listing_id = l.listing_id
    JOIN users u ON t.vendeur_id = u.user_id
    WHERE t.acheteur_id = ?
    ORDER BY t.transaction_date DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$transaction = $stmt->fetch();

if ($transaction) {
    $message = "Paiement enregistré avec succès. Merci pour votre achat !";
} else {
    $message = "Aucune transaction récente trouvée.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Paiement - BiniMarket</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/auth.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Confirmation du Paiement</h1>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="confirmation-box">
                <h2><?php echo htmlspecialchars($message); ?></h2>

                <?php if ($transaction): ?>
                    <div class="transaction-details">
                        <p><strong>Annonce :</strong> <?php echo htmlspecialchars($transaction['listing_title']); ?></p>
                        <p><strong>Montant :</strong> <?php echo number_format($transaction['amount'], 2); ?> FCFA</p>
                        <p><strong>Méthode :</strong> <?php echo htmlspecialchars($transaction['payment_method']); ?></p>
                        <p><strong>Vendeur :</strong> <?php echo htmlspecialchars($transaction['vendeur_nom']); ?></p>
                        <p><strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($transaction['transaction_date'])); ?></p>
                        <p><strong>Status :</strong> <?php echo htmlspecialchars($transaction['status']); ?></p>
                    </div>
                <?php else: ?>
                    <p>Aucune transaction trouvée.</p>
                <?php endif; ?>

                <a href="index.php" class="btn-primary">Retour à l'accueil</a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 BiniMarket. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
