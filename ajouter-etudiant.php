<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Étudiant</title>
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
        <h2>Ajouter un étudiant</h2>
        <form method="POST">
            <input type="text" name="nom" placeholder="Nom" required><br><br>
            <input type="text" name="prenom" placeholder="Prénom" required><br><br>

            <label for="groupe">Groupe :</label><br>
            <select name="groupe" required>
                <option value="">-- Choisir un groupe --</option>
                <option value="GB1">GB1</option>
                <option value="GB2">GB2</option>
                <option value="LK1">LK1</option>
                <option value="LK2">LK2</option>
            </select><br><br>

            <button type="submit">Ajouter</button>
        </form>
    </div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $groupe = $_POST['groupe'];

    $stmt = $pdo->prepare("INSERT INTO etudiants (nom, prenom, groupe) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $prenom, $groupe]);
    echo "<p style='text-align:center;'>Étudiant ajouté avec succès !</p>";
}
?>
