<?php
require_once 'includes/db.php';

$sql = "SELECT TOP 1 * FROM Usuario"; // Cambia por una tabla que tengas
$stmt = $conn->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($row);
echo "</pre>";
?>
