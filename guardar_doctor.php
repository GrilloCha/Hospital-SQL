<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['idEmpleado'])) {
    die("Empleado no definido. Primero registre el empleado.");
}

$idEmpleado = $_SESSION['idEmpleado'];
$idEspecialidad = $_POST['idEspecialidad'];
$cedulaProfesional = $_POST['cedulaProfesional'];
estatusActividad = isset($_POST['estatusActividad']) ? 1 : 0;

$sql = "INSERT INTO Doctor (idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad) VALUES (?, ?, ?, ?)";
$params = array($idEmpleado, $idEspecialidad, $cedulaProfesional, $estatusActividad);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("Error al registrar en tabla Doctor: " . print_r(sqlsrv_errors(), true));
}

// Opcional: limpiar sesión
unset($_SESSION['idEmpleado']);
unset($_SESSION['idTipoEmpleado']);

echo "Doctor registrado correctamente.";
?>
