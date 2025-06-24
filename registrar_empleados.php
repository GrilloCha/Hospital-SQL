<?php
// Conexión a la base de datos SQL Server
include("conexion.php");

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Conexión fallida: " . print_r(sqlsrv_errors(), true));
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idTipoEmpleado = $_POST["idTipoEmpleado"];
    $idUsuario = $_POST["idUsuario"];
    $salario = $_POST["salario"];

    // Insertar en Empleado
    $sql = "INSERT INTO Empleado (idTipoEmpleado, idUsuario, salario) VALUES (?, ?, ?)";
    $params = [$idTipoEmpleado, $idUsuario, $salario];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        $mensaje = "✅ Empleado registrado correctamente.";
    } else {
        $mensaje = "❌ Error al registrar empleado: " . print_r(sqlsrv_errors(), true);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrar Empleado</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        :root {
            --color-text-heading: #1f2937;
            --color-text-body: #6b7280;
            --color-white: #ffffff;
            --color-black: #111827;
            --border-radius: 0.75rem;
            --shadow-light: rgba(0, 0, 0, 0.05);
            --transition-speed: 0.3s;
        }

        /* Reset & base setup */
        * {
            box-sizing: border-box;
        }
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background: var(--color-white);
            color: var(--color-text-body);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container for the form */
        .form-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 2.5rem 3rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px var(--shadow-light);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Heading */
        h2 {
            font-weight: 600;
            font-size: 2.25rem;
            margin-bottom: 2rem;
            color: var(--color-text-heading);
            user-select: none;
        }

        /* Form */
        form {
            display: flex;
            flex-direction: column;
            gap: 1.3rem;
        }

        label {
            font-weight: 600;
            color: var(--color-text-heading);
            text-align: left;
            margin-bottom: 0.3rem;
            user-select: none;
            font-size: 1rem;
        }

        input[type="number"],
        select {
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 2px solid var(--color-black);
            border-radius: var(--border-radius);
            outline-offset: 2px;
            transition: border-color var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
            color: var(--color-text-body);
            font-family: inherit;
            appearance: none;
            background: var(--color-white);
            width: 100%; /* Ensure full width */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }

        input[type="number"]:focus,
        select:focus {
            border-color: var(--color-black);
            box-shadow: 0 0 8px var(--color-black);
        }

        /* Button */
        input[type="submit"] {
            background: var(--color-black);
            color: var(--color-white);
            border: none;
            padding: 0.85rem 1rem;
            font-size: 1.15rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
            user-select: none;
            box-shadow: 0 4px 12px var(--shadow-light);
            width: 100%; /* Ensure full width */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }
        input[type="submit"]:hover,
        input[type="submit"]:focus {
            background: #27272a;
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.45);
            outline: none;
        }
        input[type="submit"]:active {
            transform: scale(0.98);
        }

        /* Button for redirection */
        .redirect-buttons {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .redirect-buttons button {
            background: var(--color-black);
            color: var(--color-white);
            border: none;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
            user-select: none;
            box-shadow: 0 4px 12px var(--shadow-light);
            width: 100%; /* Ensure full width */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }

        .redirect-buttons button:hover,
        .redirect-buttons button:focus {
            background: #27272a;
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.45);
            outline: none;
        }
        .redirect-buttons button:active {
            transform: scale(0.98);
        }

        /* Responsive tweaks */
        @media (max-width: 500px) {
            .form-container {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }
            h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <section class="form-container" aria-label="Formulario de registro de empleado">
        <h2>Registrar Empleado</h2>
        <form method="post">
            <div>
                <label>ID de Usuario:</label>
                <input type="number" name="idUsuario" required>
            </div>
            <div>
                <label>Tipo de Empleado:</label>
                <select name="idTipoEmpleado" required>
                    <option value="">-- Selecciona --</option>
                    <option value="1">Recepcionista</option>
                    <option value="2">Doctor</option>
                </select>
            </div>
            <div>
                <label>Salario:</label>
                <input type="number" step="0.01" name="salario" required>
            </div>
            <input type="submit" value="Registrar">
        </form>
        <div class="redirect-buttons">
            <button onclick="location.href='login.php'">Iniciar Sesión</button>
            <button onclick="location.href='registro.php'">Registrar Usuario</button>
        </div>
    </section>
</body>
</html>
