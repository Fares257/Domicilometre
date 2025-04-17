<?php
include('config.php');
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT ville, lat, lon FROM domiciles");
$domiciles = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($domiciles);
?>