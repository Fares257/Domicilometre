<?php
include('config.php');

$group_name = $_POST['groupe'] ?? null;
$etudiants = [];
$medians = [];

if ($group_name) {
    $stmt = $pdo->prepare("
        SELECT e.*, d.type, d.date_debut, d.date_fin, v.nom AS nom_ville,
               v.nb_hab_1999, v.nb_hab_2010, v.nb_hab_2012
        FROM etudiants e
        LEFT JOIN domiciliations d ON e.id = d.etudiant_id
        LEFT JOIN villes v ON d.ville = v.nom
        WHERE e.groupe = ?
        ORDER BY e.nom
    ");
    $stmt->execute([$group_name]);
    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        $etudiants[$row['id']]['info'] = $row;
        $etudiants[$row['id']]['domiciles'][] = $row;

        if ($row['nb_hab_2012']) {
            $medians[] = (int)$row['nb_hab_2012'];
        }
    }

    sort($medians);
    $count = count($medians);
    $median = $count ? ($count % 2 === 0
        ? ($medians[$count / 2 - 1] + $medians[$count / 2]) / 2
        : $medians[floor($count / 2)]
    ) : null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir Groupe</title>
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
        <h2>Voir un groupe</h2>
        <form method="POST">
            <select name="groupe" required>
                <option value="">-- Choisir un groupe --</option>
                <option value="GB1" <?= ($group_name == 'GB1') ? 'selected' : '' ?>>GB1</option>
                <option value="GB2" <?= ($group_name == 'GB2') ? 'selected' : '' ?>>GB2</option>
                <option value="LK1" <?= ($group_name == 'LK1') ? 'selected' : '' ?>>LK1</option>
                <option value="LK2" <?= ($group_name == 'LK2') ? 'selected' : '' ?>>LK2</option>
            </select>
            <button type="submit">Voir</button>
        </form>
    </div>

    <?php if ($group_name): ?>
        <h3>Groupe : <?= htmlspecialchars($group_name) ?></h3>

        <?php if ($median !== null): ?>
            <p><strong>Médiane des habitants en 2012 :</strong> <?= number_format($median, 0, ',', ' ') ?> hab</p>
        <?php endif; ?>

        <div class="form-container">
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Domiciles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etudiants as $etu): ?>
                        <tr>
                            <td><?= htmlspecialchars($etu['info']['nom']) ?></td>
                            <td><?= htmlspecialchars($etu['info']['prenom']) ?></td>
                            <td>
                                <?php foreach ($etu['domiciles'] as $dom): ?>
                                    <div>
                                        <strong><?= ucfirst($dom['type']) ?>:</strong>
                                        <?= htmlspecialchars($dom['nom_ville']) ?> |
                                        1999: <?= $dom['nb_hab_1999'] ?> |
                                        2010: <?= $dom['nb_hab_2010'] ?> |
                                        2012: <?= $dom['nb_hab_2012'] ?> 
                                        (<?= $dom['date_debut'] ?> à <?= $dom['date_fin'] ?>)
                                    </div>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
