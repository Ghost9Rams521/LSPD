<?php
session_start(); // Démarrer la session

include 'db_connection_2.php'; // Inclure la connexion à la base de données

$results = []; // Variable pour stocker les résultats de la recherche
$message = ''; // Variable pour stocker les messages d'erreur

// Traitement du formulaire de mise à jour
if (isset($_POST['update'])) {
    // Récupérer les données du formulaire
    $id = $_POST['id_personne'] ?? '';
    $photo = $_POST['photo'] ?? '';
    $age = $_POST['age'] ?? '';
    $profession = $_POST['profession'] ?? '';
    $organisation = $_POST['organisation'] ?? '';
    $gang = $_POST['gang'] ?? '';
    $dossier = $_POST['dossier'] ?? '';

    // Gérer le téléchargement de fichiers
    if (!empty($_FILES['photo_file']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo_file"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérifier si le fichier est une image réelle
        $check = getimagesize($_FILES["photo_file"]["tmp_name"]);
        if ($check === false) {
            $message = "Le fichier n'est pas une image.";
            $uploadOk = 0;
        }

        // Vérifier la taille du fichier
        if ($_FILES["photo_file"]["size"] > 500000) {
            $message = "Le fichier est trop lourd.";
            $uploadOk = 0;
        }

        // Autoriser certains formats d'image
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $message = "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
            $uploadOk = 0;
        }

        // Vérifier si $uploadOk est défini sur 0 par une erreur
        if ($uploadOk == 0) {
            $message = "Désolé, le fichier n'a pas été téléchargé.";
        } else {
            // Si tout est bon, essayer de télécharger le fichier
            if (move_uploaded_file($_FILES["photo_file"]["tmp_name"], $target_file)) {
                $photo = $target_file;
                $message = "L'image a été mise à jour avec succès.";
            } else {
                $message = "Désolé, une erreur est survenue lors du téléchargement du fichier.";
            }
        }
    }

    // Préparer la requête de mise à jour
    $sql = "UPDATE personne SET 
                Photo = IFNULL(NULLIF(?, ''), Photo), 
                Age = IFNULL(NULLIF(?, ''), Age), 
                Profession = IFNULL(NULLIF(?, ''), Profession), 
                Organisation = IFNULL(NULLIF(?, ''), Organisation), 
                Gang = IFNULL(NULLIF(?, ''), Gang), 
                Dossier = IFNULL(NULLIF(?, ''), Dossier)
            WHERE ID_personne = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssssi', $photo, $age, $profession, $organisation, $gang, $dossier, $id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "<p>CV mis à jour avec succès.</p>";
        } else {
            $_SESSION['message'] = "<p>Erreur lors de la mise à jour du CV.</p>";
        }
        
        $stmt->close();
    } else {
        $_SESSION['message'] = "<p>Erreur de préparation de la requête.</p>";
    }

    // Rediriger pour afficher le message sur la même page
    header('Location: BDD.php');
    exit;
}

// Traitement du formulaire de recherche
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update'])) {
    // Récupérer la saisie de l'utilisateur
    $search = $_POST['nom'] ?? '';

    if ($search) {
        // Requête pour rechercher la personne en fonction du nom, prénom, âge, profession, organisation ou gang
        $sql = "SELECT * FROM personne WHERE nom LIKE ? OR prenom LIKE ? OR age LIKE ? OR profession LIKE ? OR organisation LIKE ? OR gang LIKE ?";
        if ($stmt = $conn->prepare($sql)) {
            $search_param = "%$search%";
            $stmt->bind_param('ssssss', $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
            $stmt->execute();
            $result = $stmt->get_result();

            // Si des résultats sont trouvés, les stocker dans la variable $results
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $results[] = $row;
                }
            } else {
                $message = "Aucun résultat trouvé.";
            }
            
            $stmt->close();
        } else {
            $message = "Erreur de préparation de la requête.";
        }
    }
}

// Gestion du répertoire d'uploads
$uploadDir = 'uploads/';

if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        $directoryMessage = "<div class='message success'>Le répertoire '$uploadDir' a été créé avec succès.</div>";
    } else {
        $directoryMessage = "<div class='message error'>Échec de la création du répertoire '$uploadDir'. Veuillez vérifier les permissions.</div>";
    }
} else {
    $directoryMessage = "<div class='message info'>Le répertoire '$uploadDir' existe déjà.</div>";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Base de données</title>
    <link rel="stylesheet" href="style.css">
    <script src="scripts/main.js" defer></script>
    <style>
        /* Intégration du CSS dans la balise <style> pour simplifier la démonstration */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .content {
            margin-left: 250px; /* Compensate la largeur de la sidebar */
            padding: 20px;
            box-sizing: border-box;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .search-form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .search-form label {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }
        .search-form input[type="text"] {
            width: calc(100% - 22px);
            padding: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .search-form button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 10px; /* Ajout d'un espace sous le bouton de recherche */
        }
        .search-form button:hover {
            background-color: #0056b3;
        }
        .create-cv-button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .create-cv-button:hover {
            background-color: #218838;
        }
        .cv-page {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            flex: 1;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .message.info {
            background-color: #cce5ff;
            color: #004085;
        }
        .cv-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
            max-width: 800px;
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        .cv-card img {
            border-radius: 50%;
            margin-right: 20px;
            width: 150px;
            height: 150px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .cv-card h3 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .cv-card p {
            margin: 5px 0;
            color: #555;
        }
        .cv-card button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .cv-card button:hover {
            background-color: #0056b3;
        }
        .update-form {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }
        .update-form label {
            margin-bottom: 5px;
            color: #333;
        }
        .update-form input, .update-form textarea {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .update-form button {
            align-self: flex-start;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .update-form button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
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
        <div class="search-form">
            <h1>Recherche</h1>
            <form method="POST" action="BDD.php">
                <label for="nom">Rechercher une personne:</label>
                <input type="text" id="nom" name="nom" placeholder="Entrez un nom, prénom, profession..." required>
                <button type="submit">Rechercher</button>
                <a href="generation_BDD.php" class="create-cv-button">Créer un CV</a>
            </form>
        </div>

        <div class="cv-page">
            <!-- Affichage des messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message info"><?= $_SESSION['message'] ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="message info"><?= $message ?></div>
            <?php endif; ?>

            <!-- Affichage des résultats -->
            <?php if (!empty($results)): ?>
                <ul class="results-list">
                    <?php foreach ($results as $person): ?>
                        <li>
                            <div class="cv-card">
                                <img src="<?= htmlspecialchars($person['Photo']) ?>?t=<?= time() ?>" alt="Photo de <?= htmlspecialchars($person['Nom']) ?>">
                                <div>
                                    <h3><?= htmlspecialchars($person['Nom']) ?> <?= htmlspecialchars($person['Prenom']) ?></h3>
                                    <p><strong>Âge:</strong> <?= htmlspecialchars($person['Age']) ?></p>
                                    <p><strong>Profession:</strong> <?= htmlspecialchars($person['Profession']) ?></p>
                                    <p><strong>Organisation:</strong> <?= htmlspecialchars($person['Organisation']) ?></p>
                                    <p><strong>Gang:</strong> <?= htmlspecialchars($person['Gang']) ?></p>
                                    <p><strong>Dossier:</strong> <?= htmlspecialchars($person['Dossier']) ?></p>
                                    
                                    <!-- Formulaire de mise à jour -->
                                    <form class="update-form" method="POST" action="BDD.php" enctype="multipart/form-data">
                                        <input type="hidden" name="id_personne" value="<?= htmlspecialchars($person['id_personne']) ?>">
                                        <label for="photo_file">Mettre à jour la photo:</label>
                                        <input type="file" name="photo_file" id="photo_file">
                                        <label for="age">Mettre à jour l'âge:</label>
                                        <input type="text" name="age" id="age" value="<?= htmlspecialchars($person['Age']) ?>">
                                        <label for="profession">Mettre à jour la profession:</label>
                                        <input type="text" name="profession" id="profession" value="<?= htmlspecialchars($person['Profession']) ?>">
                                        <label for="organisation">Mettre à jour l'organisation:</label>
                                        <input type="text" name="organisation" id="organisation" value="<?= htmlspecialchars($person['Organisation']) ?>">
                                        <label for="gang">Mettre à jour le gang:</label>
                                        <input type="text" name="gang" id="gang" value="<?= htmlspecialchars($person['Gang']) ?>">
                                        <label for="dossier">Mettre à jour le dossier:</label>
                                        <textarea name="dossier" id="dossier"><?= htmlspecialchars($person['Dossier']) ?></textarea>
                                        <button type="submit" name="update">Mettre à jour</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>