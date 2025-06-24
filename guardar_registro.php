<?php
include("conexion.php");

// Recibir y limpiar datos
$nombre = trim($_POST['nombre'] ?? '');
$apellidoP = trim($_POST['apellidoP'] ?? '');
$apellidoM = trim($_POST['apellidoM'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');
$curp = trim($_POST['curp'] ?? '');
$fechaNacimiento = trim($_POST['fechaNacimiento'] ?? '');
$domicilio = trim($_POST['domicilio'] ?? '');
$celular = trim($_POST['celular'] ?? '');

// Validación simple
if ($nombre === '' || $apellidoP === '' || $correo === '' || $contrasena === '' || 
    $curp === '' || $fechaNacimiento === '' || $domicilio === '' || $celular === '') {
    die("❌ Error: Por favor rellena todos los campos obligatorios. <a href='registro.php'>Volver</a>");
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    die("❌ Error: El correo no es válido. <a href='registro.php'>Volver</a>");
}

if (strlen($curp) !== 16) {
    die("❌ Error: CURP debe tener 16 caracteres. <a href='registro.php'>Volver</a>");
}

$dateObj = date_create($fechaNacimiento);
if (!$dateObj) {
    die("❌ Error: Fecha de nacimiento no válida. <a href='registro.php'>Volver</a>");
}

$sql_verifica = "SELECT * FROM Usuario WHERE correoElectronico = ? OR curp = ?";
$params_verifica = [$correo, $curp];
$stmt_verifica = sqlsrv_query($conn, $sql_verifica, $params_verifica);

if ($stmt_verifica && sqlsrv_has_rows($stmt_verifica)) {
    die("❌ El correo o la CURP ya están registrados. <a href='registro.php'>Volver</a>");
}

// Insertar usuario
$sql = "INSERT INTO Usuario 
        (idTipoUsuario, nombre, apellidoPaterno, apellidoMaterno, contrasenaLogin, correoElectronico, curp, fechaNacimiento, domicilio, celularContacto)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$idTipoUsuario = 1; // tipo paciente
$params = [$idTipoUsuario, $nombre, $apellidoP, $apellidoM, $contrasena, $correo, $curp, $fechaNacimiento, $domicilio, $celular];

$stmt = sqlsrv_query($conn, $sql, $params);
//GIARDAR ID
if ($stmt) { 
$sql_id = "SELECT SCOPE_IDENTITY() AS idUsuario";
    $stmt_id = sqlsrv_query($conn, $sql_id);
    if ($stmt_id) {
        sqlsrv_fetch($stmt_id);
        $idUsuario = sqlsrv_get_field($stmt_id, 0);
    }
}
if ($stmt) { 
    $sql_id = "SELECT SCOPE_IDENTITY() AS idUsuario";
    $stmt_id = sqlsrv_query($conn, $sql_id);
    if ($stmt_id) {
        sqlsrv_fetch($stmt_id);
        $idUsuario = sqlsrv_get_field($stmt_id, 0);
    }

    // ✅ Redirige al registro con mensaje de éxito
    header("Location: registro.php?exito=1");
    exit();
}

?>

