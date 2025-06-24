<?php
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiar cualquier salida previa
ob_start();

if ($_POST && isset($_POST['login'])) {
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (empty($correo) || empty($password)) {
        $_SESSION['error'] = "Por favor complete todos los campos";
        header("Location: login.php");
        exit();
    }
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
        $_SESSION['error'] = "Error al conectar con la base de datos.";
        header("Location: login.php");
        exit();
    }
    // Consulta para validar usuario y obtener tipo
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
    $params = [$correo];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        $_SESSION['error'] = "Error al consultar el usuario: " . print_r(sqlsrv_errors(), true);
        header("Location: login.php");
        exit();
    }
    $usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    // Validar contraseña
    if ($usuario && $usuario['contrasenaLogin'] === $password) {
        $_SESSION['id_usuario'] = $usuario['idUsuario'];
        $_SESSION['usuario'] = $usuario['nombre'] . ' ' . $usuario['apellidoPaterno']; // Cambiado de 'usuario_nombre' a 'usuario'
        $_SESSION['tipo_usuario'] = $usuario['tipoUsuario'];
        $_SESSION['usuario_logueado'] = true;
        
        // DEBUGGING - Agregar esto temporalmente
        error_log("Login exitoso - Tipo: " . $usuario['tipoUsuario']);
        error_log("Empleado ID: " . ($usuario['idEmpleado'] ?? 'NULL'));
        error_log("Rol Empleado: " . ($usuario['rolEmpleado'] ?? 'NULL'));
        
        // Si es empleado, guardar también sus datos
        if (!empty($usuario['idEmpleado'])) {
            $_SESSION['empleado_id'] = $usuario['idEmpleado'];
            $_SESSION['rol_empleado'] = strtolower($usuario['rolEmpleado']); // Convertir a minúsculas
            error_log("Rol guardado en sesión: " . $_SESSION['rol_empleado']);
        }
        
        // Cerrar conexión antes de redireccionar
        sqlsrv_close($conn);
        
        // Redirección según tipo y rol
        if ($usuario['tipoUsuario'] === 'Paciente') {
            error_log("Redirigiendo a paciente.php");
            header("Location: paciente.php");
            exit();
        } elseif ($usuario['tipoUsuario'] === 'Empleado') {
            // Convertir a minúsculas para la comparación
            $rolEmpleado = strtolower($usuario['rolEmpleado']);
            error_log("Procesando empleado con rol: " . $rolEmpleado);
            
            if ($rolEmpleado === 'recepcionista') {
                error_log("Redirigiendo a recepcionista.php");
                header("Location: recepcionista.php");
                exit();
            } elseif ($rolEmpleado === 'doctor') {
                error_log("Redirigiendo a doctor.php");
                header("Location: doctor.php");
                exit();
            } else {
                error_log("Rol no reconocido: " . $usuario['rolEmpleado']);
                $_SESSION['error'] = "Rol de empleado no reconocido: " . $usuario['rolEmpleado'];
                header("Location: login.php");
                exit();
            }
        } else {
            error_log("Tipo de usuario no válido: " . $usuario['tipoUsuario']);
            $_SESSION['error'] = "Tipo de usuario no válido: " . $usuario['tipoUsuario'];
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Correo o contraseña incorrectos.";
        header("Location: login.php");
        exit();
    }
}

// Si llegamos aquí sin redirección, algo salió mal
$_SESSION['error'] = "Error procesando el login.";
header("Location: login.php");
exit();
?>