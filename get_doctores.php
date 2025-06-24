<?php
require 'conexion.php';
if (!$conn) {
    die(json_encode(["error" => "❌ No hay conexión con la base de datos."]));
}


if (!isset($_GET['idEspecialidad'])) {
    echo json_encode([]);
    exit;
}

$idEspecialidad = intval($_GET['idEspecialidad']);

// Consulta doctores que tienen esa especialidad
$sql = "
    SELECT D.idDoctor, U.nombre, U.apellidoPaterno
    FROM Doctor D
    INNER JOIN Empleado E ON D.idEmpleado = E.idEmpleado
    INNER JOIN Usuario U ON E.idUsuario = U.idUsuario
    WHERE D.idEspecialidad = ?
";
$params = [$idEspecialidad];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode(["error" => sqlsrv_errors()]);
    exit;
}

$doctores = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $doctores[] = [
        "idDoctor" => $row['idDoctor'],
        "nombre" => $row['nombre'],
        "apellidoPaterno" => $row['apellidoPaterno']
    ];
}

echo json_encode($doctores);
