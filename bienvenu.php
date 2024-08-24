<?php
session_start(); // Démarrer la session

// Vérifier si le nom est stocké dans la session
if (isset($_SESSION['nom'])) {
    $nom = htmlspecialchars($_SESSION['nom']); // Sécuriser le nom
} else {
    $nom = "Nom non spécifié";
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Barre latérale -->
        <aside class="sidebar_bienvenu">
            <h2>MENU</h2>
            <ul class="main-menu">
                <!-- Éléments en haut -->
                <li><a href="#">Accueil</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="BDD.php">Base de données</a></li>
                <li><a href="#">Amende</a></li>
                <li><a href="#">Rapport</a></li>
            </ul>
            <ul class="sidebar_2">
                <!-- Éléments en bas -->
                <li><a href="#">Profil</a></li>
                <li><a href="tools.php">Paramètres</a></li>
                <li><a href="index.html">Déconnexion</a></li>
            </ul>
        </aside>

        <!-- Contenu principal -->
        <main class="content">
            <section class="hero">
                <h1>Bienvenue, <?php echo $nom; ?> !</h1>
            </section>

            <section class="about">
                <h2>À PROPOS</h2>
                <div class="about-content">
                    <img src="logo_LSPD.jpg" class="about-image">
                    <div class="about-text">
                        <p>Bienvenue à la Los Santos Police Department (LSPD), le pilier de la sécurité et de la justice dans la dynamique et vibrante ville de Los Santos. En tant que force de police municipale, la LSPD est engagée à assurer la sécurité publique, à maintenir l'ordre et à protéger les citoyens contre le crime et la violence.</p>
                        <p>Notre mission est de servir et de protéger la communauté avec intégrité, professionnalisme et respect. Nous nous engageons à appliquer la loi de manière équitable, à promouvoir la paix et à garantir un environnement sûr pour tous les habitants de la ville.</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>