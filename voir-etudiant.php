<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir un étudiant</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
    </style>
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
    <h2>Visualiser un étudiant</h2>
    <form method="GET">
        <label for="etudiant_id">Sélectionnez un étudiant :</label><br>
        <select name="etudiant_id" required>
            <option value="">-- Choisir --</option>
            <?php
            $etudiants = $pdo->query("SELECT id, nom, prenom FROM etudiants")->fetchAll();
            foreach ($etudiants as $etudiant) {
                echo "<option value='{$etudiant['id']}'";
                if (isset($_GET['etudiant_id']) && $_GET['etudiant_id'] == $etudiant['id']) echo " selected";
                echo ">{$etudiant['prenom']} {$etudiant['nom']}</option>";
            }
            ?>
        </select><br><br>
        <button type="submit">Afficher</button>
    </form>
</div>

<?php
if (isset($_GET['etudiant_id'])) {
    $id = $_GET['etudiant_id'];

    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
    $stmt->execute([$id]);
    $etudiant = $stmt->fetch();

    echo "<div class='form-container'>";
    echo "<h3>Informations sur l'étudiant</h3>";
    echo "<p><strong>Nom :</strong> {$etudiant['nom']}</p>";
    echo "<p><strong>Prénom :</strong> {$etudiant['prenom']}</p>";
    echo "<p><strong>Groupe :</strong> {$etudiant['groupe']}</p>";

    echo "<h4>Domiciles :</h4>";
    $residences = $pdo->prepare("
        SELECT d.*, v.nom AS nom_ville, v.nb_hab_1999, v.nb_hab_2010, v.nb_hab_2012
        FROM domiciliations d
        JOIN villes v ON d.ville = v.nom  -- La colonne 'ville' dans domiciliations contient le nom de la ville
        WHERE d.etudiant_id = ?
    ");
    $residences->execute([$id]);

    if ($residences->rowCount() > 0) {
        echo "<table>";
        echo "<thead><tr><th>Type de domicile</th><th>Ville</th><th>Période</th><th>Population</th><th>Variation</th></tr></thead><tbody>";
        
        foreach ($residences as $dom) {
            $p99 = $dom['nb_hab_1999'];
            $p10 = $dom['nb_hab_2010'];
            $p12 = $dom['nb_hab_2012'];
            
            $variation = "";
            if ($p99 < $p10 && $p10 < $p12) $variation = "Haussière 📈";
            elseif ($p99 > $p10 && $p10 > $p12) $variation = "Baissière 📉";
            elseif ($p99 == $p10 && $p10 == $p12) $variation = "Plane ➖";
            elseif ($p99 > $p10 && $p12 > $p10) $variation = "Cuvette ⬊⬈";
            elseif ($p99 < $p10 && $p12 < $p10) $variation = "Monticule ⬈⬊";
            else $variation = "Indéfinie";

            // Affichage dans un tableau pour plus de lisibilité
            echo "<tr>";
            echo "<td>{$dom['type']}</td>";
            echo "<td>{$dom['nom_ville']}</td>";
            echo "<td><span class='bold'>{$dom['date_debut']} - {$dom['date_fin']}</span></td>"; 
            echo "<td><span class='bold'>1999: $p99, 2010: $p10, 2012: $p12</span></td>";   
            echo "<td><strong>$variation</strong></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Aucun domicile trouvé pour cet étudiant.</p>";
    }
    echo "</div>";
}
?>
</body>
</html>
