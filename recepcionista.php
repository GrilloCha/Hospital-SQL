<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol_empleado'] !=  'recepcionista') {
    echo "❌ Acceso denegado.";
    exit();
}

// Verificar que realmente sea recepcionista
include("conexion.php");
$sql = "SELECT idTipoEmpleado FROM Empleado WHERE idUsuario = ?";
$stmt = sqlsrv_query($conn, $sql, [$_SESSION['id_usuario']]);
$empleado = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($empleado['idTipoEmpleado'] != 1) {
    echo "⚠️ Solo recepcionistas pueden acceder aquí.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recepcionista</title>
</head>
<body>
    <h2>Bienvenida, <?php echo $_SESSION['usuario']; ?> (Recepcionista)</h2>

    <p>Aquí puedes registrar doctores y gestionar citas.</p>

    <a href="registro_doctor.php">Registrar un Doctor</a><br><br>
    <a href="registrar_empleados.php">Registrar un empleado(No doctores)</a><br><br>
    <a href="registro.php">Registrar un usuario</a><br><br>
    <a href="generar_ticket.php">Generar ticket de compra</a>
    
    <form action="logout.php" method="post">
        <button type="submit">Cerrar Sesión</button>
    </form>
</body>
</html>
