<?php
session_start();

// Validar que el usuario esté logueado y sea tipo 'paciente'
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'Paciente') {
    echo "❌ Acceso denegado.";
    exit();
}

require 'conexion.php';

$idUsuario = $_SESSION['id_usuario'];

if (!$idUsuario) {
    echo "No se encontró ID de usuario en sesión.";
    exit();
}

// Obtener idPaciente según idUsuario
$sql = "SELECT idPaciente FROM Paciente WHERE idUsuario = ?";
$stmt = sqlsrv_query($conn, $sql, [$idUsuario]);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$idPaciente = null;
if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $idPaciente = $row['idPaciente'];
} else {
    echo "No se encontró paciente relacionado.";
    exit();
}

// Obtener citas del paciente
$sqlCitas = "
    SELECT folioCita, fechaCita, nombre, apellidoPaterno, apellidoMaterno, cedulaProfesional, estatusCita
    FROM VistaCitasPaciente
    WHERE idPaciente = ?
    ORDER BY fechaCita DESC
";

$stmtCitas = sqlsrv_query($conn, $sqlCitas, [$idPaciente]);
if ($stmtCitas === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Obtener datos personales del paciente
$sqlDatos = "SELECT * FROM VistaDatosPersonalesPaciente WHERE idPaciente = ?";
$stmtDatos = sqlsrv_query($conn, $sqlDatos, [$idPaciente]);
if ($stmtDatos === false) {
    die(print_r(sqlsrv_errors(), true));
}

$datosPaciente = sqlsrv_fetch_array($stmtDatos, SQLSRV_FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel Paciente</title>
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

    /* Reset and base */
    *, *::before, *::after {
      box-sizing: border-box;
    }
    body, html {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: var(--color-white);
      color: var(--color-text-body);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      padding: 4rem 1rem;
    }

    .container {
      max-width: 1200px;
      width: 100%;
      background: var(--color-white);
      border-radius: var(--border-radius);
      box-shadow: 0 4px 12px var(--shadow-light);
      padding: 3rem 3.5rem;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      gap: 2.5rem;
    }

    h2 {
      font-weight: 700;
      font-size: 3rem;
      color: var(--color-text-heading);
      margin: 0;
      user-select: none;
      line-height: 1.1;
    }

    h3 {
      font-weight: 600;
      font-size: 1.75rem;
      color: var(--color-text-heading);
      margin-bottom: 1rem;
      user-select: none;
      border-bottom: 1px solid #e5e7eb;
      padding-bottom: 0.3rem;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 0.5rem;
      font-size: 1rem;
      color: var(--color-text-body);
    }

    thead tr {
      background: var(--color-table-header-bg);
      font-weight: 600;
      color: var(--color-text-heading);
      border-radius: var(--border-radius);
    }

    thead tr th {
      padding: 1rem 1.5rem;
      text-align: left;
      user-select: none;
    }

    tbody tr {
      background: var(--color-white);
      transition: background-color var(--transition-speed);
      box-shadow: 0 1px 3px var(--shadow-md);
      border-radius: var(--border-radius);
    }

    tbody tr:hover {
      background-color: var(--color-table-row-hover);
    }

    tbody tr td {
      padding: 1rem 1.5rem;
      vertical-align: middle;
    }

    a {
      font-weight: 600;
      color: var(--color-black);
      text-decoration: none;
      transition: color var(--transition-speed);
    }
    a:hover,
    a:focus {
      color: var(--color-text-heading);
      text-decoration: underline;
      outline: none;
    }

    form {
      margin-top: 1rem;
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
      user-select: none;
      transition: background-color var(--transition-speed), transform var(--transition-speed);
      box-shadow: 0 4px 8px var(--shadow-md);
      display: inline-block;
    }

    button:hover,
    button:focus {
      background-color: #27272a;
      outline: none;
      transform: scale(1.05);
      box-shadow: 0 6px 12px rgba(39, 39, 42, 0.8);
    }

    button:active {
      transform: scale(0.97);
    }

    @media (max-width: 768px) {
      .container {
        padding: 2rem 2rem;
      }
      h2 {
        font-size: 2.25rem;
      }
      h3 {
        font-size: 1.5rem;
      }
      table thead tr th,
      table tbody tr td {
        padding: 0.75rem 1rem;
      }
    }

    @media (max-width: 400px) {
      button {
        width: 100%;
        padding: 0.75rem;
      }
    }
  </style>
</head>
<body>
  <main class="container" role="main" aria-label="Panel de Paciente">
    <h2>Bienvenido(a), <?php echo htmlspecialchars($_SESSION['usuario']); ?> (Paciente)</h2>

    <section aria-labelledby="datos-personales-title">
      <h3 id="datos-personales-title">Datos personales</h3>
      <table role="table">
        <tbody>
          <tr><td><strong>Nombre completo:</strong></td>
              <td><?php echo htmlspecialchars($datosPaciente['nombre'] . ' ' . $datosPaciente['apellidoPaterno'] . ' ' . $datosPaciente['apellidoMaterno']); ?></td></tr>
          <tr><td><strong>Fecha de nacimiento:</strong></td>
              <td><?php echo $datosPaciente['fechaNacimiento'] ? $datosPaciente['fechaNacimiento']->format('Y-m-d') : ''; ?></td></tr>
          <tr><td><strong>Curp:</strong></td>
              <td><?php echo htmlspecialchars($datosPaciente['curp']); ?></td></tr>
          <tr><td><strong>Correo electrónico:</strong></td>
              <td><?php echo htmlspecialchars($datosPaciente['correoElectronico']); ?></td></tr>
          <tr><td><strong>Teléfono:</strong></td>
              <td><?php echo htmlspecialchars($datosPaciente['celularContacto']); ?></td></tr>
          <tr><td><strong>Dirección:</strong></td>
              <td><?php echo htmlspecialchars($datosPaciente['domicilio']); ?></td></tr>
        </tbody>
      </table>
    </section>

    <section aria-labelledby="citas-title">
      <h3 id="citas-title">Tus citas</h3>
      <table role="table">
        <thead>
          <tr>
            <th>Folio</th>
            <th>Fecha Cita</th>
            <th>Doctor</th>
            <th>Cédula Profesional</th>
            <th>Estatus</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($cita = sqlsrv_fetch_array($stmtCitas, SQLSRV_FETCH_ASSOC)) : ?>
          <tr>
            <td><?php echo htmlspecialchars($cita['folioCita']); ?></td>
            <td><?php echo $cita['fechaCita'] ? $cita['fechaCita']->format('Y-m-d H:i') : ''; ?></td>
            <td><?php echo htmlspecialchars($cita['nombre'] . ' ' . $cita['apellidoPaterno'] . ' ' . $cita['apellidoMaterno']); ?></td>
            <td><?php echo htmlspecialchars($cita['cedulaProfesional']); ?></td>
            <td><?php echo $cita['estatusCita'] ? 'Activa' : 'Cancelada'; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>

    <p><a href="agendar_cita.php">Agendar nueva cita</a></p>

    <form action="logout.php" method="post">
      <button type="submit">Cerrar Sesión</button>
    </form>
  </main>
</body>
</html>
