<?php
$serverName = "localhost";
$connectionOptions = array(
    "Database" => "NombreDeTuBaseDeDatos",
    "Uid" => "usuario",
    "PWD" => "contraseña"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn) {
    echo "Conexión exitosa a SQL Server.";
} else {
    echo "Error en la conexión.";
    die(print_r(sqlsrv_errors(), true));
}
?>

