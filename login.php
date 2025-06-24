<?php
session_start();

// Manejo de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Obtener error y limpiar
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hospital - Sistema de Login</title>
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

        .login-container {
            background: var(--color-white);
            padding: 2.5rem 3rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px var(--shadow-light);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--color-text-heading);
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.3rem;
            text-align: left;
        }

        label {
            font-weight: 600;
            color: var(--color-text-heading);
            margin-bottom: 0.3rem;
            font-size: 1rem;
        }

        input[type="email"],
        input[type="password"] {
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 2px solid var(--color-black);
            border-radius: var(--border-radius);
            background: var(--color-white);
            color: var(--color-text-body);
            width: 100%;
            box-sizing: border-box;
        }

        input:focus {
            border-color: var(--color-black);
            box-shadow: 0 0 8px var(--color-black);
            outline: none;
        }

        button[type="submit"] {
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
            width: 100%;
            box-sizing: border-box;
        }

        button:hover,
        button:focus {
            background: #27272a;
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.45);
        }

        .error-message {
            color: #e11d48;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
        }

        .success-message {
            color: #22c55e;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
        }

        .user-info {
            margin-top: 1rem;
            text-align: left;
            font-size: 0.95rem;
            color: var(--color-text-heading);
        }

        .user-info p {
            margin: 0.2rem 0;
        }

        .logout-btn, .login-btn {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background: var(--color-black);
            color: var(--color-white);
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
        }

        .logout-btn:hover, .login-btn:hover {
            background: #27272a;
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado']): ?>
        <div class="success-message">¡Bienvenido de nuevo!</div>

        <div class="user-info">
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($_SESSION['tipo_usuario']); ?></p>
            <?php if (!empty($_SESSION['rol_empleado'])): ?>
                <p><strong>Rol:</strong> <?php echo htmlspecialchars(ucfirst($_SESSION['rol_empleado'])); ?></p>
            <?php endif; ?>


            <?php
            // Definir URL según tipo y rol
            $url_panel = '#';
            if (strtolower($_SESSION['tipo_usuario']) === 'paciente') {
                $url_panel = 'paciente.php';
            } elseif (strtolower($_SESSION['tipo_usuario']) === 'empleado' && isset($_SESSION['rol_empleado'])) {
                if ($_SESSION['rol_empleado'] === 'doctor') {
                    $url_panel = 'doctor.php';
                } elseif ($_SESSION['rol_empleado'] === 'recepcionista') {
                    $url_panel = 'recepcionista.php';
                }
            }
            ?>
            <a href="<?php echo $url_panel; ?>" class="btn-panel" style="margin-right: 10px;">Ir a mi panel</a>
            <a href="?logout=1" class="logout-btn">Cerrar Sesión</a>
        </div>

    <?php else: ?>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="procesar_login.php" novalidate>
            <label for="correo">Correo Electrónico:</label>
            <input
                type="email"
                id="correo"
                name="correo"
                required
                placeholder="ejemplo@hospital.com"
                value="<?= isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : '' ?>"
            />

            <label for="password">Contraseña:</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                placeholder="Ingrese su contraseña"
            />

            <button type="submit" name="login">Iniciar Sesión</button>
        </form>

        <form method="POST" action="registro.php" style="margin-top: 1rem;">
            <button type="submit" name="redireccionar">Registrarse</button>
        </form>
    <?php endif; ?>
</body>

</html>

