<?php
session_start(); // Démarrer la session

// Vérifier si le nom est stocké dans la session
if (isset($_SESSION['nom'])) {
    $nom = htmlspecialchars($_SESSION['nom']); // Sécuriser le nom
} else {
    $nom = "Nom non spécifié";
}

// Inclure le fichier de connexion à la base de données
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les valeurs du formulaire
    $mot_de_passe_actuel = $_POST['mot_de_passe_actuel'];
    $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'];
    $confirmation_mot_de_passe = $_POST['confirmation_mot_de_passe'];
    
    // Assumer que le nom d'utilisateur est stocké dans la session
    $utilisateur = $_SESSION['nom'];
    
    // Vérifier les mots de passe
    $erreur = '';
    if (empty($mot_de_passe_actuel) || empty($nouveau_mot_de_passe) || empty($confirmation_mot_de_passe)) {
        $erreur = "Tous les champs doivent être remplis.";
    } else if ($nouveau_mot_de_passe !== $confirmation_mot_de_passe) {
        $erreur = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        // Récupérer le mot de passe actuel de la base de données
        $query = "SELECT mdp FROM informations WHERE nom = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $utilisateur);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $erreur = "Utilisateur non trouvé.";
        } else {
            $row = $result->fetch_assoc();
            $mot_de_passe_actuel_stocke = $row['mdp'];
            
            // Vérifier si le mot de passe actuel est correct
            if ($mot_de_passe_actuel !== $mot_de_passe_actuel_stocke) {
                $erreur = "Le mot de passe actuel est incorrect.";
            } else {
                // Mettre à jour le mot de passe
                $query = "UPDATE informations SET mdp = ? WHERE nom = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $nouveau_mot_de_passe, $utilisateur);
                
                if ($stmt->execute()) {
                    $erreur = "Mot de passe mis à jour avec succès.";
                } else {
                    $erreur = "Une erreur est survenue lors de la mise à jour du mot de passe.";
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPD - Changer de mot de passe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Barre latérale -->
        <aside class="sidebar_bienvenu">
            <h2>MENU</h2>
            <ul class="main-menu">
                <!-- Éléments en haut -->
                <li><a href="bienvenu.php">Accueil</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="BDD.php">Base de données</a></li>
                <li><a href="#">Amende</a></li>
                <li><a href="#">Rapport</a></li>
            </ul>
            <ul class="sidebar_2">
                <!-- Éléments en bas -->
                <li><a href="#">Profil</a></li>
                <li><a href="#">Paramètres</a></li>
                <li><a href="index.html">Déconnexion</a></li>
            </ul>
        </aside>

        <!-- Contenu principal -->
        <main class="content">
            <section class="hero">
                <h1>Bienvenue, <?php echo $nom; ?> !</h1>
            </section>

            <section class="about">
                <h2>Paramètre</h2>
                <br>
                <?php if (!empty($erreur)): ?>
                    <p class="erreur"><?php echo $erreur; ?></p>
                <?php endif; ?>
                <form method="post" action="">
                    <h3>Changer de mot de passe :</h3>
                    <br>
                    <label for="mot_de_passe_actuel">Mot de passe actuel:</label>
                    <input type="password" id="mot_de_passe_actuel" name="mot_de_passe_actuel" required>
                    <br><br>
                    <label for="nouveau_mot_de_passe">Nouveau mot de passe:</label>
                    <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required>
                    <br><br>
                    <label for="confirmation_mot_de_passe">Confirmer le nouveau mot de passe:</label>
                    <input type="password" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" required>
                    <br><br>
                    <button type="submit">Changer le mot de passe</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>