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
    header('Location: recherche_CV.php');
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

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rechercher et Mettre à Jour un CV</title>
    <style>
        /* Ajoutez ici votre style pour le formulaire de recherche et mise à jour */
    </style>
</head>
<body>
    <div class="container">
        <h1>Rechercher et Mettre à Jour un CV</h1>

        <form method="POST" action="recherche_CV.php" class="search-form">
            <label for="nom">Nom, prénom, âge, profession, organisation ou gang :</label>
            <input type="text" id="nom" name="nom" placeholder="Entrez un nom, prénom, âge, profession, organisation ou gang">
            <button type="submit" name="search">Lancer la Recherche</button>
        </form>

        <!-- Affichage des messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Affichage des résultats -->
        <?php if (!empty($results)): ?>
            <div class="cv-container">
                <h3>Résultats de la recherche :</h3>
                <?php foreach ($results as $person): ?>
                    <div class="cv-item">
                        <div class="cv-header">
                            <?php if (!empty($person['Photo'])): ?>
                                <img src="<?= htmlspecialchars($person['Photo']) ?>" alt="Photo de <?= htmlspecialchars($person['Nom']) ?>">
                            <?php else: ?>
                                <img src="default-avatar.png" alt="Photo par défaut">
                            <?php endif; ?>
                            <div>
                                <h1><?= htmlspecialchars($person['Nom']) ?> <?= htmlspecialchars($person['Prenom']) ?></h1>
                                <p><strong>Âge :</strong> <?= htmlspecialchars($person['Age']) ?></p>
                                <p><strong>Profession :</strong> <?= htmlspecialchars($person['Profession']) ?></p>
                            </div>
                        </div>
                        <div class="section">
                            <h2>Organisation et Gang</h2>
                            <p><strong>Organisation :</strong> <?= htmlspecialchars($person['Organisation']) ?></p>
                            <p><strong>Gang :</strong> <?= htmlspecialchars($person['Gang']) ?></p>
                        </div>
                        <hr>
                        <div class="section">
                            <h2>Dossier</h2>
                            <p><strong>Dossier :</strong> <?= htmlspecialchars($person['Dossier']) ?></p>
                        </div>
                        <hr>

                        <!-- Formulaire de mise à jour -->
                        <div class="update-form">
                            <h3>Mettre à jour le CV</h3>
                            <form method="POST" action="recherche_CV.php">
                                <input type="hidden" name="id_personne" value="<?= htmlspecialchars($person['id_personne']) ?>">

                                <!-- Label et champ pour la photo -->
                                <label for="photo">URL de la Photo :</label>
                                <input type="text" id="photo" name="photo" placeholder="URL de la Photo" value="<?= htmlspecialchars($person['Photo']) ?>">

                                <!-- Label et champ pour l'âge -->
                                <label for="age">Âge :</label>
                                <input type="text" id="age" name="age" placeholder="Âge" value="<?= htmlspecialchars($person['Age']) ?>">

                                <!-- Label et champ pour la profession -->
                                <label for="profession">Profession :</label>
                                <input type="text" id="profession" name="profession" placeholder="Profession" value="<?= htmlspecialchars($person['Profession']) ?>">

                                <!-- Label et champ pour l'organisation -->
                                <label for="organisation">Organisation :</label>
                                <input type="text" id="organisation" name="organisation" placeholder="Organisation" value="<?= htmlspecialchars($person['Organisation']) ?>">

                                <!-- Label et champ pour le gang -->
                                <label for="gang">Gang :</label>
                                <input type="text" id="gang" name="gang" placeholder="Gang" value="<?= htmlspecialchars($person['Gang']) ?>">

                                <!-- Label et champ pour le dossier -->
                                <label for="dossier">Dossier :</label>
                                <textarea id="dossier" name="dossier" placeholder="Dossier"><?= htmlspecialchars($person['Dossier']) ?></textarea>

                                <button type="submit" name="update">Mettre à Jour</button>
                            </form>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($message)): ?>
            <p class="error-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>