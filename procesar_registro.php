<?php
session_start();
$tipo_usuario = $_POST['tipo_usuario'];

if ($tipo_usuario == 'empleado') {
    // Redirigir a verificación de empleado
    header("Location: verificar_empleado.php");
    exit();
} else {
    // Procesar como paciente directamente
    // Guardar en base de datos y redirigir al login
    // ...
    header("Location: login.php");
    exit();
}
