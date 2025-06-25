<?php
// Cambiar este correo por el de tu recepcionista
$correo_recepcionista = "ana.gomez@hospital.com"; // Ajusta según tu caso

// Conexión a SQL Server
$serverName = "localhost";
$connectionOptions = [
    "Database" => "BASEHOSPITAL1",
    "Uid" => "hospitaluser",
    "PWD" => "NuevaContraseñaSegura123",
    "CharacterSet" => "UTF-8"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die("Error al conectar: " . print_r(sqlsrv_errors(), true));
}

// La misma consulta que usas en procesar_login.php
$sql = "
    SELECT u.idUsuario, u.idTipoUsuario, u.nombre, u.apellidoPaterno, 
           u.apellidoMaterno, u.contrasenaLogin, tu.tipoUsuario,
           e.idEmpleado, e.idTipoEmpleado, te.tipoEmpleado AS rolEmpleado
    FROM Usuario u
    INNER JOIN TipoUsuario tu ON u.idTipoUsuario = tu.idTipoUsuario
    LEFT JOIN Empleado e ON u.idUsuario = e.idUsuario
    LEFT JOIN TipoEmpleado te ON e.idTipoEmpleado = te.idTipoEmpleado
    WHERE u.correoElectronico = ?
";

$params = [$correo_recepcionista];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("Error en la consulta: " . print_r(sqlsrv_errors(), true));
}

$usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

echo "<h2>Datos de la consulta para: $correo_recepcionista</h2>";
echo "<pre>";
print_r($usuario);
echo "</pre>";

// Verificar cada campo específico
echo "<h3>Verificación de campos:</h3>";
echo "idUsuario: " . ($usuario['idUsuario'] ?? 'NULL') . "<br>";
echo "tipoUsuario: " . ($usuario['tipoUsuario'] ?? 'NULL') . "<br>";
echo "idEmpleado: " . ($usuario['idEmpleado'] ?? 'NULL') . "<br>";
echo "rolEmpleado: " . ($usuario['rolEmpleado'] ?? 'NULL') . "<br>";

// Verificar si el usuario existe en la tabla Empleado
echo "<h3>Verificación en tabla Empleado:</h3>";
$sql_emp = "SELECT e.*, te.tipoEmpleado FROM Empleado e 
            LEFT JOIN TipoEmpleado te ON e.idTipoEmpleado = te.idTipoEmpleado 
            WHERE e.idUsuario = ?";
$stmt_emp = sqlsrv_query($conn, $sql_emp, [$usuario['idUsuario']]);
$empleado = sqlsrv_fetch_array($stmt_emp, SQLSRV_FETCH_ASSOC);

echo "<pre>";
print_r($empleado);
echo "</pre>";

sqlsrv_close($conn);
?>