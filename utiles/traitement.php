<?php

    // Récupération des données du formulaire
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = htmlspecialchars($_POST['name'] ?? '');
        $email = htmlspecialchars($_POST['email'] ?? '');
        $telephone = htmlspecialchars($_POST['phone'] ?? '');
        $sujet = htmlspecialchars($_POST['subject'] ?? '');
        $message = htmlspecialchars($_POST['message'] ?? '');

        // Adresse email de réception
        $to = "djoubablaise743@gmail.com", "imaslawgarga@gmail.com"; // Remplace par ton adresse email

        // Sujet de l'email
        $subject = "Nouveau message du formulaire de contact : $sujet";

        // Corps du message
        $body = "Nom complet: $nom\n";
        $body .= "Email: $email\n";
        $body .= "Téléphone: $telephone\n";
        $body .= "Sujet: $sujet\n";
        $body .= "Message:\n$message";

        // Entêtes (headers)
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";

        // Envoi de l'email
        if (mail($to, $subject, $body, $headers)) {
            echo "Message envoyé avec succès.";
        } else {
            echo "Une erreur s'est produite lors de l'envoi.";
        }
    }

    //header("Location: merci.html"); // redirige vers une page de remerciement
    exit();

?>
