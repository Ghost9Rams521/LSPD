<?php
$servername = "localhost";
$username = "root"; // Votre nom d'utilisateur MySQL
$password = ""; // Votre mot de passe MySQL
$database = "personne"; // Remplacez par le nom correct de votre base de données

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>