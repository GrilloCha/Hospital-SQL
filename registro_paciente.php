<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registro de Usuario</title>
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
            --input-height: 2.5rem;
        }

        *, *::before, *::after {
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
            flex-direction: column;
            align-items: center;
        }

        header {
            width: 100%;
            max-width: 1200px;
            padding: 3.5rem 1.5rem 1.5rem;
        }

        header h1 {
            font-family: 'Roboto Slab', serif;
            font-weight: 700;
            font-size: 3.5rem;
            color: var(--color-text-heading);
            margin: 0;
        }

        main {
            width: 100%;
            max-width: 500px;
            background: var(--color-white);
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px var(--shadow-light);
            padding: 2.5rem 3rem;
            margin-bottom: 4rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            font-weight: 600;
            color: var(--color-text-heading);
            font-size: 1rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        select {
            padding: 0 0.75rem;
            height: var(--input-height);
            font-size: 0.875rem;
            border: 2px solid var(--color-black);
            border-radius: var(--border-radius);
            color: var(--color-text-body);
            width: 100%;
        }

        input:focus,
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
            margin-top: 1rem;
            min-width: 140px;
        }

        button:hover {
            background: #27272a;
            transform: scale(1.05);
        }

        .mensaje-exito {
            background-color: #e0ffe0;
            color: #065f46;
            padding: 1rem;
            border: 1px solid #065f46;
            border-radius: 10px;
            margin-top: 1rem;
            text-align: center;
        }

        .mensaje-exito a button {
            background: #111827;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            header h1 {
                font-size: 2.8rem;
            }
            main {
                margin: 0 1rem 3rem;
                padding: 2rem;
            }
            button[type="submit"] {
                width: 100%;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Registro de Usuario</h1>
    </header>

    <main>
        <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
            <div class="mensaje-exito">
                ✅ Usuario registrado correctamente.
                <br><br>
                <a href="login.php">
                    <button>Iniciar Sesión</button>
                </a>
            </div>
        <?php else: ?>
            <form action="guardar_registro.php" method="post" novalidate>
                <div>
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required />
                </div>
                <div>
                    <label for="apellidoP">Apellido Paterno:</label>
                    <input type="text" id="apellidoP" name="apellidoP" required />
                </div>
                <div>
                    <label for="apellidoM">Apellido Materno:</label>
                    <input type="text" id="apellidoM" name="apellidoM" />
                </div>
                <div>
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required />
                </div>
                <div>
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required />
                </div>
                <div>
                    <label for="curp">CURP:</label>
                    <input type="text" id="curp" name="curp" maxlength="18" required />
                </div>
                <div>
                    <label for="fechaNacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fechaNacimiento" name="fechaNacimiento" required />
                </div>
                <div>
                    <label for="domicilio">Domicilio:</label>
                    <input type="text" id="domicilio" name="domicilio" required />
                </div>
                <div>
                    <label for="celular">Celular de Contacto:</label>
                    <input type="text" id="celular" name="celular" maxlength="12" required />
                </div>
            
                <button type="submit">Registrarse</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>

