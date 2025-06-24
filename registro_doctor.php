<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 1) {
    echo "Acceso no autorizado.";
    exit();
}

// Validar que sea una recepcionista
$sql = "SELECT E.idEmpleado, E.idTipoEmpleado FROM Empleado E
        WHERE E.idUsuario = ?";
$stmt = sqlsrv_query($conn, $sql, [$_SESSION['id_usuario']]);
$empleado = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($empleado['idTipoEmpleado'] != 1) {
    echo "Solo las recepcionistas pueden registrar doctores.";
    exit();
}
?>

<form action="guardar_doctor.php" method="POST">
    <input type="text" name="nombre" required placeholder="Nombre del doctor">
    <input type="email" name="correo" required placeholder="Correo">
    <input type="password" name="contrasena" required placeholder="Contraseña">
    <button type="submit">Registrar Doctor</button>
</form>
