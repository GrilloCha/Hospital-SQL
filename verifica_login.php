<?php
session_start();
include("conexion.php");

$usuario = trim($_POST['usuario']);
$contrasena = trim($_POST['contrasena']);
$rolSeleccionado = $_POST['rol'] ?? '';

if (!$rolSeleccionado) {
    die("❌ Debes seleccionar un rol para iniciar sesión.");
}

// Consulta para verificar usuario y contraseña
$sql = "SELECT * FROM Usuario WHERE correoElectronico = ? AND contrasenaLogin = ?";
$params = [$usuario, $contrasena];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt && sqlsrv_has_rows($stmt)) {
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Validar rol según el tipo de usuario
    if ($row['idTipoUsuario'] == 1 && strtolower($rolSeleccionado) == 'paciente') {
        // Paciente
        $_SESSION['usuario'] = $row['nombre'];
        $_SESSION['id_usuario'] = $row['idUsuario'];
        $_SESSION['tipo_usuario'] = 'paciente';
        header("Location: paciente.php");
        exit();

    } elseif ($row['idTipoUsuario'] == 2) {
        // Empleado (doctor o recepcionista)
        $sqlEmp = "SELECT idTipoEmpleado FROM Empleado WHERE idUsuario = ?";
        $stmtEmp = sqlsrv_query($conn, $sqlEmp, [$row['idUsuario']]);

        if ($stmtEmp && sqlsrv_has_rows($stmtEmp)) {
            $empleado = sqlsrv_fetch_array($stmtEmp, SQLSRV_FETCH_ASSOC);

            if ($empleado['idTipoEmpleado'] == 1 && strtolower($rolSeleccionado) == 'recepcionista') {
                $_SESSION['usuario'] = $row['nombre'];
                $_SESSION['id_usuario'] = $row['idUsuario'];
                $_SESSION['tipo_usuario'] = 'recepcionista';
                header("Location: recepcionista.php");
                exit();

            } elseif ($empleado['idTipoEmpleado'] == 2 && strtolower($rolSeleccionado) == 'doctor') {
                $_SESSION['usuario'] = $row['nombre'];
                $_SESSION['id_usuario'] = $row['idUsuario'];
                $_SESSION['tipo_usuario'] = 'doctor';
                header("Location: doctor.php");
                exit();

            } else {
                echo "❌ El rol seleccionado no coincide con tu tipo de usuario.";
            }

        } else {
            echo "❌ No se encontró el empleado.";
        }

    } else {
        echo "❌ El rol seleccionado no coincide con tu tipo de usuario.";
    }

} else {
    echo "❌ Usuario o contraseña incorrectos.";
}
?>
