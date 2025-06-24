<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    // Consulta para validar usuario y obtener tipo y rol
    $sql = "
        SELECT u.idUsuario, u.idTipoUsuario, u.nombre, u.apellidoPaterno, u.apellidoMaterno,
               u.contrasenaLogin, tu.tipoUsuario,
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
        // Sesiones consistentes
        $_SESSION['id_usuario'] = $usuario['idUsuario'];
        $_SESSION['usuario'] = $usuario['nombre'] . ' ' . $usuario['apellidoPaterno'];
        $_SESSION['tipo_usuario'] = strtolower($usuario['tipoUsuario']); // Ej. "paciente"
        $_SESSION['usuario_logueado'] = true;

        if (!empty($usuario['idEmpleado'])) {
            $_SESSION['empleado_id'] = $usuario['idEmpleado'];
            $_SESSION['rol_empleado'] = strtolower($usuario['rolEmpleado']); // Ej. "doctor"
        }

        // Redirección por tipo de usuario
        if ($_SESSION['tipo_usuario'] === 'paciente') {
                var_dump($_SESSION['tipo_usuario']);
                var_dump($_SESSION['rol_empleado']);
                exit();
        } elseif ($_SESSION['tipo_usuario'] === 'empleado') {
            if ($_SESSION['rol_empleado'] === 'recepcionista') {
                var_dump($_SESSION['tipo_usuario']);
                var_dump($_SESSION['rol_empleado']);
                exit();
            } elseif ($_SESSION['rol_empleado'] === 'doctor') {
                var_dump($_SESSION['tipo_usuario']);
                var_dump($_SESSION['rol_empleado']);
                exit();
            } else {
                $_SESSION['error'] = "Rol de empleado no reconocido.";
                var_dump($_SESSION['tipo_usuario']);
                var_dump($_SESSION['rol_empleado']);
                exit();
            }
        } else {
            $_SESSION['error'] = "Tipo de usuario no válido.";
            header("Location: login.php");
            exit();
        }

    } else {
        $_SESSION['error'] = "Correo o contraseña incorrectos.";
        header("Location: login.php");
        exit();
    }
}
