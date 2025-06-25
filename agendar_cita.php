<?php
session_start();

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

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idEspecialidad'], $_POST['idDoctor'], $_POST['fechaCita'], $_POST['horaCita'])) {
    $idEspecialidad = intval($_POST['idEspecialidad']);
    $idDoctor = intval($_POST['idDoctor']);
    $fechaCitaStr = $_POST['fechaCita'];
    $horaCitaStr = $_POST['horaCita'];

    $fechaHoraCita = DateTime::createFromFormat('Y-m-d H:i', "$fechaCitaStr $horaCitaStr");

    if (!$fechaHoraCita) {
        $error = "Fecha u hora no válida.";
    } else {
        $sqlCheckPendiente = "SELECT 1 FROM Cita WHERE idDoctor = ? AND idPaciente = ? AND estatusCita = 'Pendiente'";
        $stmtPendiente = sqlsrv_query($conn, $sqlCheckPendiente, [$idDoctor, $idPaciente]);

        if (sqlsrv_fetch_array($stmtPendiente)) {
            $error = "Ya tienes una cita pendiente con este doctor.";
        } else {
            $hoy = new DateTime();
            if ($fechaHoraCita < $hoy) {
                $error = "No puedes agendar una cita en una fecha pasada.";
            } elseif ($fechaHoraCita < (clone $hoy)->add(new DateInterval('PT48H'))) {
                $error = "La cita debe agendarse con al menos 48 horas de anticipación.";
            } elseif ($fechaHoraCita > (clone $hoy)->add(new DateInterval('P3M'))) {
                $error = "La cita no puede ser agendada con más de 3 meses de anticipación.";
            } else {
                $diaSemana = $fechaHoraCita->format('dddd');

                $sqlHorario = "
                    SELECT 1 FROM JornadaLaboral JL
                    INNER JOIN Doctor D ON D.idEmpleado = JL.idEmpleado
                    WHERE D.idDoctor = ?
                      AND JL.diaSemana = ?
                      AND ? BETWEEN JL.horaInicio AND JL.horaFin";
                $paramsHorario = [$idDoctor, $diaSemana, $fechaHoraCita->format('H:i:s')];
                $stmtHorario = sqlsrv_query($conn, $sqlHorario, $paramsHorario);

                if (!sqlsrv_fetch_array($stmtHorario)) {
                    $error = "El doctor no atiende en ese horario.";
                } else {
                    $sqlOcupado = "SELECT 1 FROM Cita WHERE idDoctor = ? AND fechaCita = ? AND horaCita = ?";
                    $paramsOcupado = [$idDoctor, $fechaHoraCita->format('Y-m-d'), $fechaHoraCita->format('H:i:s')];
                    $stmtOcupado = sqlsrv_query($conn, $sqlOcupado, $paramsOcupado);
                    if (sqlsrv_fetch_array($stmtOcupado)) {
                        $error = "El doctor ya tiene una cita en esa fecha y hora.";
                    } else {
                        // Iniciar transacción
                        sqlsrv_begin_transaction($conn);
                        
                        // Obtener el idEmpleado del doctor para la bitácora
                        $sqlEmpleado = "SELECT idEmpleado FROM Doctor WHERE idDoctor = ?";
                        $stmtEmpleado = sqlsrv_query($conn, $sqlEmpleado, [$idDoctor]);
                        $idEmpleado = null;
                        if ($rowEmpleado = sqlsrv_fetch_array($stmtEmpleado, SQLSRV_FETCH_ASSOC)) {
                            $idEmpleado = $rowEmpleado['idEmpleado'];
                        }
                        
                        // Insertar la cita
                        $sqlInsert = "INSERT INTO Cita (idDoctor, idPaciente, fechaCita, estatusCita, horaCita) VALUES (?, ?, ?, ?, ?)";
                        $paramsInsert = [$idDoctor, $idPaciente, $fechaHoraCita->format('Y-m-d'), 'Pendiente', $fechaHoraCita->format('H:i:s')];
                        $stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);

                        if ($stmtInsert === false) {
                            sqlsrv_rollback($conn);
                            $errors = sqlsrv_errors();
                            $errorMsg = $errors ? $errors[0]['message'] : 'Error desconocido al guardar la cita.';
                            $error = "Error al guardar la cita: " . htmlspecialchars($errorMsg);
                        } else {
                            // Obtener el folioCita recién insertado
                            $sqlFolio = "SELECT SCOPE_IDENTITY() as folioCita";
                            $stmtFolio = sqlsrv_query($conn, $sqlFolio);
                            $folioCita = null;
                            if ($rowFolio = sqlsrv_fetch_array($stmtFolio, SQLSRV_FETCH_ASSOC)) {
                                $folioCita = $rowFolio['folioCita'];
                            }
                            
                            // Insertar en la bitácora
                            if ($folioCita && $idEmpleado) {
                                $sqlBitacora = "INSERT INTO Bitacora (idPaciente, idEmpleado, folioCita, fechaCita) VALUES (?, ?, ?, ?)";
                                $paramsBitacora = [$idPaciente, $idEmpleado, $folioCita, $fechaHoraCita->format('Y-m-d H:i:s')];
                                $stmtBitacora = sqlsrv_query($conn, $sqlBitacora, $paramsBitacora);
                                
                                if ($stmtBitacora === false) {
                                    sqlsrv_rollback($conn);
                                    $errors = sqlsrv_errors();
                                    $errorMsg = $errors ? $errors[0]['message'] : 'Error desconocido al guardar en bitácora.';
                                    $error = "Error al guardar en bitácora: " . htmlspecialchars($errorMsg);
                                } else {
                                    sqlsrv_commit($conn);
                                    $success = "✅ Cita agendada correctamente para el " . $fechaHoraCita->format('Y-m-d H:i');
                                }
                            } else {
                                sqlsrv_rollback($conn);
                                $error = "Error al obtener información necesaria para la bitácora.";
                            }
                        }
                    }
                }
            }
        }
    }
}

// Cargar especialidades para el formulario
$sqlEspecialidades = "SELECT idEspecialidad, nombreEspecialidad FROM Especialidad ORDER BY nombreEspecialidad";
$stmtEspecialidades = sqlsrv_query($conn, $sqlEspecialidades);
$especialidades = [];
while ($row = sqlsrv_fetch_array($stmtEspecialidades, SQLSRV_FETCH_ASSOC)) {
    $especialidades[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Agendar Nueva Cita</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        :root {
            --color-text-heading: #1f2937;
            --color-text-body: #6b7280;
            --color-white: #ffffff;
            --color-black: #111827;
            --border-radius: 0.75rem;
            --shadow-light: rgba(0, 0, 0, 0.05);
            --transition-speed: 0.3s;
            --input-height: 2.5rem; /* Smaller input height as requested */
        }

        /* Reset & base setup */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background: url('hospital_background.jpg') no-repeat center center fixed; /* Replace with your hospital image */
            background-size: cover;
            color: var(--color-text-body);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.9); /* White background with slight transparency */
            padding: 2.5rem 3rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px var(--shadow-light);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h2 {
            font-weight: 600;
            font-size: 2.25rem;
            margin-bottom: 2rem;
            color: var(--color-text-heading);
            user-select: none;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            text-align: left;
        }

        label {
            font-weight: 600;
            color: var(--color-text-heading);
            margin-bottom: 0.3rem;
            user-select: none;
            font-size: 1rem;
            display: block;
        }

        input[type="date"],
        select {
            padding: 0 0.75rem;
            height: var(--input-height);
            font-size: 0.875rem;
            border: 2px solid var(--color-black);
            border-radius: var(--border-radius);
            outline-offset: 2px;
            transition: border-color var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
            color: var(--color-text-body);
            font-family: inherit;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="date"]:focus,
        select:focus {
            border-color: var(--color-black);
            box-shadow: 0 0 8px var(--color-black);
        }

        button[type="submit"] {
            background: var(--color-black);
            color: var(--color-white);
            border: none;
            padding: 0.75rem 1rem;
            font-size: 1.125rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
            user-select: none;
            box-shadow: 0 4px 12px var(--shadow-light);
            margin-top: 1rem;
            align-self: flex-start;
            min-width: 140px;
        }

        button[type="submit"]:hover,
        button[type="submit"]:focus {
            background: #27272a;
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.45);
            outline: none;
        }

        button[type="submit"]:active {
            transform: scale(0.98);
        }

        a {
            color: var(--color-black);
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-top: 0.5rem;
        }

        .success {
            color: green;
            margin-top: 0.5rem;
        }

        /* Styles for the select elements */
        select {
            appearance: none; /* Remove default arrow */
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%236b7280"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 0.75rem top 50%;
            background-size: 1.25rem;
            padding-right: 2.5rem; /* Space for the arrow */
        }

        /* Remove focus outline for select elements */
        select:focus {
            outline: none;
            border-color: var(--color-black);
            box-shadow: 0 0 8px var(--color-black);
        }

        /* Responsive tweaks */
        @media (max-width: 600px) {
            .container {
                max-width: 100%;
                margin: 1rem;
                padding: 2rem;
            }
            button[type="submit"] {
                width: 100%;
                min-width: unset;
                font-size: 1rem;
                padding: 0.75rem;
            }
        }
    </style>
    <script>
        function cargarDoctores() {
            var idEspecialidad = document.getElementById('idEspecialidad').value;
            if (!idEspecialidad) {
                document.getElementById('idDoctor').innerHTML = '<option value="">--Selecciona Especialidad primero--</option>';
                return;
            }
            fetch('get_doctores.php?idEspecialidad=' + idEspecialidad)
                .then(response => response.json())
                .then(data => {
                    let opciones = '<option value="">--Selecciona Doctor--</option>';
                    data.forEach(doc => {
                        opciones += `<option value="${doc.idDoctor}">${doc.nombre} ${doc.apellidoPaterno}</option>`;
                    });
                    document.getElementById('idDoctor').innerHTML = opciones;
                })
                .catch(err => alert('Error al cargar doctores: ' + err));
        }

        function cargarHorarios() {
            var idDoctor = document.getElementById('idDoctor').value;
            var fecha = document.getElementById('fechaCita').value;
            if (!idDoctor || !fecha) {
                document.getElementById('horaCita').innerHTML = '<option value="">--Selecciona doctor y fecha primero--</option>';
                return;
            }
            fetch(`get_horarios.php?idDoctor=${idDoctor}&fecha=${fecha}`)
                .then(response => response.json())
                .then(data => {
                    let opciones = '<option value="">--Selecciona Horario--</option>';
                    data.forEach(hora => {
                        opciones += `<option value="${hora}">${hora}</option>`;
                    });
                    document.getElementById('horaCita').innerHTML = opciones;
                })
                .catch(err => alert('Error al cargar horarios: ' + err));
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Agendar Nueva Cita</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (!empty($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="idEspecialidad">Especialidad:</label>
            <select id="idEspecialidad" name="idEspecialidad" onchange="cargarDoctores()" required>
                <option value="">--Selecciona Especialidad--</option>
                <?php foreach ($especialidades as $esp): ?>
                    <option value="<?php echo $esp['idEspecialidad']; ?>"><?php echo htmlspecialchars($esp['nombreEspecialidad']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="idDoctor">Doctor:</label>
            <select id="idDoctor" name="idDoctor" onchange="cargarHorarios()" required>
                <option value="">--Selecciona Especialidad primero--</option>
            </select>

            <label for="fechaCita">Fecha de Cita:</label>
            <input type="date" id="fechaCita" name="fechaCita" onchange="cargarHorarios()" min="<?php echo (new DateTime('+2 days'))->format('Y-m-d'); ?>" max="<?php echo (new DateTime('+90 days'))->format('Y-m-d'); ?>" required>

            <label for="horaCita">Horario:</label>
            <select id="horaCita" name="horaCita" required>
                <option value="">--Selecciona doctor y fecha primero--</option>
            </select>

            <button type="submit">Agendar Cita</button>
        </form>

        <p><a href="paciente.php">Volver al panel</a></p>
    </div>
</body>
</html>