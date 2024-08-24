<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Démarrer la session si elle n'est pas déjà active

include 'db_connection_2.php'; // Inclure la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $age = $_POST['age'] ?? '';
    $profession = $_POST['profession'] ?? '';
    $organisation = $_POST['organisation'] ?? '';
    $gang = $_POST['gang'] ?? '';
    $dossier = $_POST['dossier'] ?? '';

    // Gestion de l'upload de photo
    $photo = ''; // Initialiser la variable photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Définir les extensions de fichiers autorisées
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = __DIR__ . '/uploaded_files/';
            $dest_path = $uploadFileDir . $fileName;

            // Debug: Afficher les chemins
            echo "<p>Chemin de destination : $dest_path</p>";
            echo "<p>Chemin temporaire : $fileTmpPath</p>";

            // Vérifier si le répertoire existe et est accessible en écriture
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true); // Crée le répertoire si nécessaire
            }
            
            // Déboguer les permissions du répertoire
            echo "<p>Permissions du répertoire : " . substr(sprintf('%o', fileperms($uploadFileDir)), -4) . "</p>";

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $photo = $fileName; // Juste le nom du fichier, non le chemin complet
            } else {
                $_SESSION['message'] = "<p>Erreur lors de l'upload de la photo. Impossible de déplacer le fichier.</p>";
                header('Location: generation_BDD.php');
                exit;
            }
        } else {
            $_SESSION['message'] = "<p>Extension de fichier non autorisée.</p>";
            header('Location: generation_BDD.php');
            exit;
        }
    } else {
        // Déboguer les codes d'erreur
        $errorCodes = [
            UPLOAD_ERR_INI_SIZE => 'La taille du fichier excède la directive upload_max_filesize.',
            UPLOAD_ERR_FORM_SIZE => 'La taille du fichier excède la directive MAX_FILE_SIZE.',
            UPLOAD_ERR_PARTIAL => 'Le fichier a été téléchargé partiellement.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé.',
            UPLOAD_ERR_NO_TMP_DIR => 'Le répertoire temporaire est manquant.',
            UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture du fichier sur le disque.',
            UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté l\'envoi du fichier.'
        ];
        
        $errorMessage = $errorCodes[$_FILES['photo']['error']] ?? 'Erreur inconnue lors de l\'upload.';
        $_SESSION['message'] = "<p>Erreur lors de l'upload de la photo. Code erreur : " . $errorMessage . "</p>";
        header('Location: generation_BDD.php');
        exit;
    }

    // Préparer la requête d'insertion
    $sql = "INSERT INTO personne (Nom, Prenom, Age, Profession, Organisation, Gang, Dossier, Photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssssss', $nom, $prenom, $age, $profession, $organisation, $gang, $dossier, $photo);

        if ($stmt->execute()) {
            $_SESSION['message'] = "<p>CV créé avec succès.</p>";
        } else {
            $_SESSION['message'] = "<p>Erreur lors de la création du CV.</p>";
        }
        
        $stmt->close();
    } else {
        $_SESSION['message'] = "<p>Erreur de préparation de la requête.</p>";
    }

    $conn->close();

    // Rediriger vers la même page pour afficher le message
    header('Location: generation_BDD.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer un CV</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .main-container {
            display: flex;
            width: 80vw;
        }
        main.content {
            margin-left: 250px; /* Espace pour la barre latérale */
            flex: 1;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 30vw;
            margin-left: 20vw;
        }
        h1 {
            margin-top: 0;
            color: #007bff;
            font-size: 28px;
            border-bottom: 2px solid #007bff;
            text-align: center;
            height: 3vh;
        }
        .form-group {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
            margin-top: 5vh;
        }
        .form-group label {
            flex: 1;
            min-width: 150px;
            margin-right: 10px;
            font-weight: bold;
            color: #333;
            font-size: 16px;
            align-self: center;
        }
        .form-group input[type="text"], .form-group textarea {
            flex: 2;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        .form-group input[type="text"]:focus, .form-group textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            align-self: flex-start;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <aside class="sidebar_bienvenu">
            <h2>MENU</h2>
            <ul class="main-menu">
                <li><a href="bienvenu.php">Accueil</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="BDD.php">Base de données</a></li>
                <li><a href="#">Amende</a></li>
                <li><a href="#">Rapport</a></li>
            </ul>
            <ul class="sidebar_2">
                <li><a href="#">Profil</a></li>
                <li><a href="tools.php">Paramètres</a></li>
                <li><a href="index.html">Déconnexion</a></li>
            </ul>
        </aside>
        <main class="content">
            <div class="container">
                <h1>Rentrer les données</h1>

                <?php
                if (isset($_SESSION['message'])):
                    $messageClass = strpos($_SESSION['message'], 'Erreur') !== false ? 'error' : '';
                ?>
                    <div class="message <?= $messageClass ?>"><?= $_SESSION['message'] ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <form method="POST" action="generation_BDD.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nom">Nom :</label>
                        <input type="text" id="nom" name="nom" required placeholder="Entrez le nom">
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" required placeholder="Entrez le prénom">
                    </div>

                    <div class="form-group">
                        <label for="age">Âge :</label>
                        <input type="text" id="age" name="age" required placeholder="Entrez l'âge">
                    </div>

                    <div class="form-group">
                        <label for="profession">Profession :</label>
                        <input type="text" id="profession" name="profession" required placeholder="Entrez la profession">
                    </div>

                    <div class="form-group">
                        <label for="organisation">Organisation :</label>
                        <input type="text" id="organisation" name="organisation" required placeholder="Entrez l'organisation">
                    </div>

                    <div class="form-group">
                        <label for="gang">Gang :</label>
                        <input type="text" id="gang" name="gang" required placeholder="Entrez le gang">
                    </div>

                    <div class="form-group">
                        <label for="dossier">Dossier :</label>
                        <textarea id="dossier" name="dossier" required placeholder="Entrez les détails du dossier"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="photo">Télécharger une Photo :</label>
                        <input type="file" id="photo" name="photo" accept="image/*" required>
                    </div>

                    <button type="submit" name="submit">Créer le CV</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>