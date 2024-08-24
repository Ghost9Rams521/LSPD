
<?php 
    session_start(); // Démarrer la session
    if (isset($_POST['bouton-valider'])){
        if (isset($_POST['nom']) && isset($_POST['mdp'])) {
     
    $nom = $_POST['nom'];
    $mdp = $_POST['mdp'];
    $erreur = "";
    $_SESSION['nom'] = $nom; 

    $nom_server = "localhost";
    $utilisateur = "root";
    $mot_de_passe = "";
    $nom_base_données = "informations";
    $con = mysqli_connect($nom_server , $utilisateur , $mot_de_passe , $nom_base_données);

    $req = mysqli_query($con, "SELECT * FROM informations WHERE nom= '$nom' and mdp= '$mdp' " );
    $num_ligne = mysqli_num_rows($req);

    if($num_ligne > 0){
        header('Location: bienvenu.php');
        }else{
            $erreur = "Nom ou Mot de passe incorectes !";
        }
 }
}
    if (isset($_POST['bouton-retour'])) {
        header('Location: index.html');
    exit;
    }
 ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="jolie.css">
</head>
<body>
    <div class="login-container">
        <section class="login-section">
            <h1>Connexion</h1>
            <?php 
                if (isset($erreur)) {
                    echo "<p class='erreur'>".$erreur."<p>";
                }
             ?>
            <form action="" class="login-form" method="POST">

                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="nom" required>

                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="mdp" required>

                <button type="submit" name="bouton-valider">Se connecter</button>
                <br>
                <button type="button" onclick="window.location.href='index.html'" >Retour</button>
            </form>
        </section>
    </div>
</body>


</html>
