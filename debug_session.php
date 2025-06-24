<?php
session_start();
echo "<h2>Estado de la Sesión</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Variables específicas:</h3>";
echo "usuario_logueado: " . (isset($_SESSION['usuario_logueado']) ? ($_SESSION['usuario_logueado'] ? 'true' : 'false') : 'no existe') . "<br>";
echo "tipo_usuario: " . ($_SESSION['tipo_usuario'] ?? 'no existe') . "<br>";
echo "rol_empleado: " . ($_SESSION['rol_empleado'] ?? 'no existe') . "<br>";
echo "usuario: " . ($_SESSION['usuario'] ?? 'no existe') . "<br>";

// Verificar el archivo de recepcionista
if (file_exists('recepcionista.php')) {
    echo "<br><strong>recepcionista.php existe ✓</strong>";
} else {
    echo "<br><strong>recepcionista.php NO existe ✗</strong>";
}
?>