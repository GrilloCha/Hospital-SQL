<?php
session_start();
include("conexion.php");
$idUsuario = $_GET['idUsuario'] ?? '';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idUsuario = $_POST['idUsuario'];
    $salario = $_POST['salario'];
    $idTipoEmpleado = $_POST['idTipoEmpleado'];

    $sql = "INSERT INTO Empleado (idTipoEmpleado, idUsuario, salario) VALUES (?, ?, ?); SELECT SCOPE_IDENTITY() as idEmpleado;";
    $params = array($idTipoEmpleado, $idUsuario, $salario);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("Error al registrar empleado: " . print_r(sqlsrv_errors(), true));
    }

    sqlsrv_next_result($stmt);
    sqlsrv_fetch($stmt);
    $idEmpleado = sqlsrv_get_field($stmt, 0);

    $_SESSION['idEmpleado'] = $idEmpleado;
    $_SESSION['idTipoEmpleado'] = $idTipoEmpleado;

    header("Location: registro_complementario.php");
    exit();
} else {
    echo "Acceso no permitido.";
}
?>
