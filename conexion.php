<?php
$serverName = "localhost";
$connectionOptions = [
    "Database" => "BASEHOSPITAL1",
    "Uid" => "hospitaluser",
    "PWD" => "NuevaContraseñaSegura123",
    "CharacterSet" => "UTF-8"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
