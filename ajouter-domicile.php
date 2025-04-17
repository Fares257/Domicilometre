<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Domicile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.html">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="ajouter-etudiant.php">Ajouter Étudiant</a></li>
                <li class="nav-item"><a class="nav-link" href="ajouter-domicile.php">Ajouter Domicile</a></li>
                <li class="nav-item"><a class="nav-link" href="voir-etudiant.php">Voir Étudiant</a></li>
                <li class="nav-item"><a class="nav-link" href="voir-groupe.php">Voir Groupe</a></li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <h2>Ajouter un domicile</h2>
        <form method="POST">
            <label>Étudiant :</label><br>
            <select name="etudiant_id" required>
                <option value="">-- Sélectionner un étudiant --</option>
                <?php
                try {
                    $etudiants = $pdo->query("SELECT id, nom, prenom FROM etudiants")->fetchAll();
                    foreach ($etudiants as $etudiant) {
                        echo "<option value='{$etudiant['id']}'>{$etudiant['prenom']} {$etudiant['nom']}</option>";
                    }
                } catch (PDOException $e) {
                    echo "<option disabled>Erreur lors du chargement</option>";
                }
                ?>
            </select><br><br>

            <label>Type de résidence :</label><br>
            <select name="type" required>
                <option value="Principale">Principale</option>
                <option value="Secondaire">Secondaire</option>
            </select><br><br>

            <label>Date de début :</label><br>
            <input type="date" name="date_debut" required><br><br>

            <label>Date de fin :</label><br>
            <input type="date" name="date_fin" required><br><br>

            <label>Ville :</label><br>
            <input type="text" name="ville" placeholder="Ville" required><br><br>

            <button type="submit" name="submit">Ajouter</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
            $etudiant_id = $_POST['etudiant_id'];
            $type = $_POST['type'];
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            $ville = $_POST['ville'];

            try {
                $stmt = $pdo->prepare("INSERT INTO domiciliations (etudiant_id, type, ville, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$etudiant_id, $type, $ville, $date_debut, $date_fin]);
                echo "<p style='text-align:center; color: green;'>✅ Domicile ajouté avec succès !</p>";
            } catch (PDOException $e) {
                echo "<p style='text-align:center; color: red;'>❌ Erreur lors de l'ajout : " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
        ?>
    </div>
</body>
</html>
