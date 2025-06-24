<?php
include("conexion.php"); // tu conexión a SQL Server

$identificacion = $_POST['identificacion'];

// Buscar al empleado en la tabla Empleado
$sql = "SELECT rol FROM Empleado WHERE identificacion = ?";
$stmt = sqlsrv_query($conn, $sql, array($identificacion));

if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $rol = $row['rol'];

    if ($rol == 'secretaria') {
        // Redirigir al formulario de registro de secretaria
        header("Location: registrar_secretaria.php?identificacion=$identificacion");
    } elseif ($rol == 'doctor') {
        // Mostrar mensaje: un doctor debe ser registrado por una secretaria
        echo "<p>Los doctores deben ser registrados por una secretaria. Contacte a su administradora.</p>";
    } else {
        echo "<p>Empleado reconocido, pero rol no permitido para auto-registro.</p>";
    }
} else {
    echo "<p>Empleado no encontrado. Contacte a recursos humanos.</p>";
}
