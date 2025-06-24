<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['idEmpleado'])) {
    die("Empleado no definido. Primero registre el empleado.");
}

$idEmpleado = $_SESSION['idEmpleado'];

$sql = "INSERT INTO Recepcionista (idEmpleado) VALUES (?)";
$params = array($idEmpleado);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("Error al registrar en tabla Recepcionista: " . print_r(sqlsrv_errors(), true));
}

// Opcional: limpiar sesión
unset($_SESSION['idEmpleado']);
unset($_SESSION['idTipoEmpleado']);

echo "Recepcionista registrado correctamente.";
?>
