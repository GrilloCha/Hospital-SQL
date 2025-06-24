<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol_empleado'] != 'doctor') {
    echo "❌ Acceso denegado.";
    exit();
}

require 'conexion.php';

$idUsuario = $_SESSION['id_usuario'];

// Obtener el idDoctor usando el idUsuario
$sqlDoctor = "
    SELECT d.idDoctor
    FROM Doctor d
    INNER JOIN Empleado e ON d.idEmpleado = e.idEmpleado
    WHERE e.idUsuario = ?
";
$stmtDoctor = sqlsrv_query($conn, $sqlDoctor, [$idUsuario]);

if ($stmtDoctor === false) {
    die(print_r(sqlsrv_errors(), true));
}

$rowDoctor = sqlsrv_fetch_array($stmtDoctor, SQLSRV_FETCH_ASSOC);
if (!$rowDoctor) {
    echo "Doctor no encontrado.";
    exit();
}

$idDoctor = $rowDoctor['idDoctor'];

// Datos personales del doctor
$sqlDatos = "SELECT * FROM VistaDatosPersonalesDoctor WHERE idDoctor = ?";
$stmtDatos = sqlsrv_query($conn, $sqlDatos, [$idDoctor]);

if ($stmtDatos === false) {
    die(print_r(sqlsrv_errors(), true));
}
$datos = sqlsrv_fetch_array($stmtDatos, SQLSRV_FETCH_ASSOC);

// Próximas citas del doctor
$sqlCitas = "SELECT * FROM VistaProximasCitasDoctores WHERE idDoctor = ? ORDER BY fechaCita";
$stmtCitas = sqlsrv_query($conn, $sqlCitas, [$idDoctor]);

if ($stmtCitas === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Doctor</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    :root {
      --color-text-heading: #1f2937;
      --color-text-body: #6b7280;
      --color-white: #ffffff;
      --color-black: #111827;
      --color-table-header-bg: #f3f4f6;
      --color-table-row-hover: #f9fafb;
      --border-radius: 0.75rem;
      --shadow-light: rgba(0, 0, 0, 0.05);
      --shadow-md: rgba(0, 0, 0, 0.1);
      --transition-speed: 0.3s;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--color-white);
      color: var(--color-text-body);
      margin: 0;
      padding: 4rem 1rem;
      display: flex;
      justify-content: center;
    }

    .container {
      width: 100%;
      max-width: 1200px;
      background: var(--color-white);
      padding: 3rem;
      border-radius: var(--border-radius);
      box-shadow: 0 4px 12px var(--shadow-light);
      display: flex;
      flex-direction: column;
      gap: 2.5rem;
    }

    h2, h3 {
      color: var(--color-text-heading);
      margin: 0;
    }

    h2 {
      font-size: 2.5rem;
    }

    h3 {
      font-size: 1.75rem;
      margin-bottom: 1rem;
      border-bottom: 1px solid #e5e7eb;
      padding-bottom: 0.3rem;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 0.5rem;
      font-size: 1rem;
    }

    thead tr {
      background: var(--color-table-header-bg);
    }

    thead th, tbody td {
      padding: 1rem 1.5rem;
      text-align: left;
    }

    tbody tr {
      background: var(--color-white);
      box-shadow: 0 1px 3px var(--shadow-md);
    }

    tbody tr:hover {
      background-color: var(--color-table-row-hover);
    }

    button {
      background-color: var(--color-black);
      color: var(--color-white);
      font-weight: 600;
      font-size: 1rem;
      padding: 0.75rem 2rem;
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: all 0.3s;
    }

    button:hover {
      background-color: #27272a;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <main class="container">
    <h2>Bienvenido, Dr. 👨‍⚕️ <?php echo htmlspecialchars($datos['nombre']); ?></h2>

    <section>
      <h3>Datos personales</h3>
      <table>
        <tbody>
          <tr><td><strong>Nombre completo:</strong></td><td><?php echo htmlspecialchars($datos['nombre'] . ' ' . $datos['apellidoPaterno'] . ' ' . $datos['apellidoMaterno']); ?></td></tr>
          <tr><td><strong>Fecha de nacimiento:</strong></td><td><?php echo $datos['fechaNacimiento'] ? $datos['fechaNacimiento']->format('Y-m-d') : ''; ?></td></tr>
          <tr><td><strong>CURP:</strong></td><td><?php echo htmlspecialchars($datos['curp']); ?></td></tr>
          <tr><td><strong>Correo electrónico:</strong></td><td><?php echo htmlspecialchars($datos['correoElectronico']); ?></td></tr>
          <tr><td><strong>Teléfono:</strong></td><td><?php echo htmlspecialchars($datos['celularContacto']); ?></td></tr>
          <tr><td><strong>Dirección:</strong></td><td><?php echo htmlspecialchars($datos['domicilio']); ?></td></tr>
          <tr><td><strong>Cédula Profesional:</strong></td><td><?php echo htmlspecialchars($datos['cedulaProfesional']); ?></td></tr>
        </tbody>
      </table>
    </section>

    <section>
      <h3>Próximas citas</h3>
      <table>
        <thead>
          <tr>
            <th>Folio</th>
            <th>Fecha</th>
            <th>Paciente</th>
            <th>Estatus</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($cita = sqlsrv_fetch_array($stmtCitas, SQLSRV_FETCH_ASSOC)) : ?>
          <tr>
            <td><?php echo htmlspecialchars($cita['folioCita']); ?></td>
            <td><?php echo $cita['fechaCita'] ? $cita['fechaCita']->format('Y-m-d H:i') : ''; ?></td>
            <td><?php echo htmlspecialchars($cita['nombrePaciente'] . ' ' . $cita['apellidoPaterno'] . ' ' . $cita['apellidoMaterno']); ?></td>
            <td><?php echo $cita['estatusCita'] ? 'Activa' : 'Cancelada'; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>

    <form action="logout.php" method="post">
      <button type="submit">Cerrar sesión</button>
    </form>
  </main>
</body>
</html>
