<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo "❌ Acceso denegado.";
    exit();
}

require 'conexion.php';

// Variables para el formulario
$serviciosSeleccionados = [];
$medicamentosSeleccionados = [];
$nombrePaciente = '';
$doctorResponsable = '';
$mostrarTicket = false;
$total = 0;
$ticketGuardado = false;
$mensajeError = '';

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombrePaciente = trim($_POST['nombre_paciente'] ?? '');
    $doctorResponsable = trim($_POST['doctor_responsable'] ?? '');
    $fechaHoy = date('Y-m-d');
    
    // Obtener servicios seleccionados
    if (isset($_POST['servicios']) && is_array($_POST['servicios'])) {
        foreach ($_POST['servicios'] as $idServicio) {
            $sqlServicio = "SELECT idServicio, nombreServicio, preciosServicio FROM Servicio WHERE idServicio = ?";
            $stmtServicio = sqlsrv_query($conn, $sqlServicio, [$idServicio]);
            if ($rowServicio = sqlsrv_fetch_array($stmtServicio, SQLSRV_FETCH_ASSOC)) {
                $serviciosSeleccionados[] = $rowServicio;
                $total += floatval($rowServicio['preciosServicio']);
            }
        }
    }
    
    // Obtener medicamentos seleccionados
    if (isset($_POST['medicamentos']) && is_array($_POST['medicamentos'])) {
        foreach ($_POST['medicamentos'] as $idMedicamento) {
            $sqlMedicamento = "SELECT idMedicamento, nombre, precioMedicamento FROM Medicamento WHERE idMedicamento = ?";
            $stmtMedicamento = sqlsrv_query($conn, $sqlMedicamento, [$idMedicamento]);
            if ($rowMedicamento = sqlsrv_fetch_array($stmtMedicamento, SQLSRV_FETCH_ASSOC)) {
                $medicamentosSeleccionados[] = $rowMedicamento;
                $total += floatval($rowMedicamento['precioMedicamento']);
            }
        }
    }
    
    if (!empty($nombrePaciente) && (!empty($serviciosSeleccionados) || !empty($medicamentosSeleccionados))) {
        // Verificar si ya existe un ticket para este paciente en esta fecha
        $sqlVerificar = "SELECT COUNT(*) as existe FROM Ticket WHERE fechaTicket = ? AND nombrePaciente = ?";
        $stmtVerificar = sqlsrv_query($conn, $sqlVerificar, [$fechaHoy, $nombrePaciente]);
        $rowVerificar = sqlsrv_fetch_array($stmtVerificar, SQLSRV_FETCH_ASSOC);
        
        if ($rowVerificar['existe'] > 0) {
            $mensajeError = "Ya existe un ticket para el paciente '$nombrePaciente' en la fecha de hoy. Por favor, use un nombre diferente o espere al día siguiente.";
        } else {
            // Guardar el ticket en la base de datos
            try {
                // Iniciar transacción
                sqlsrv_begin_transaction($conn);
                
                // Insertar ticket principal
                $sqlTicket = "INSERT INTO Ticket (fechaTicket, nombrePaciente, doctorResponsable, idUsuario, totalTicket, horaTicket) 
                             VALUES (?, ?, ?, ?, ?, ?)";
                $horaActual = date('H:i:s');
                $stmtTicket = sqlsrv_query($conn, $sqlTicket, [
                    $fechaHoy, 
                    $nombrePaciente, 
                    $doctorResponsable, 
                    $_SESSION['id_usuario'], 
                    $total, 
                    $horaActual
                ]);
                
                if (!$stmtTicket) {
                    throw new Exception("Error al insertar el ticket principal");
                }
                
                // Insertar servicios
                foreach ($serviciosSeleccionados as $servicio) {
                    $sqlTicketServicio = "INSERT INTO TicketServicio (fechaTicket, nombrePaciente, idServicio, precioServicio) 
                                         VALUES (?, ?, ?, ?)";
                    $stmtTicketServicio = sqlsrv_query($conn, $sqlTicketServicio, [
                        $fechaHoy, 
                        $nombrePaciente, 
                        $servicio['idServicio'], 
                        $servicio['preciosServicio']
                    ]);
                    
                    if (!$stmtTicketServicio) {
                        throw new Exception("Error al insertar servicio: " . $servicio['nombreServicio']);
                    }
                }
                
                // Insertar medicamentos
                foreach ($medicamentosSeleccionados as $medicamento) {
                    $sqlTicketMedicamento = "INSERT INTO TicketMedicamento (fechaTicket, nombrePaciente, idMedicamento, precioMedicamento) 
                                            VALUES (?, ?, ?, ?)";
                    $stmtTicketMedicamento = sqlsrv_query($conn, $sqlTicketMedicamento, [
                        $fechaHoy, 
                        $nombrePaciente, 
                        $medicamento['idMedicamento'], 
                        $medicamento['precioMedicamento']
                    ]);
                    
                    if (!$stmtTicketMedicamento) {
                        throw new Exception("Error al insertar medicamento: " . $medicamento['nombre']);
                    }
                }
                
                // Confirmar transacción
                sqlsrv_commit($conn);
                $ticketGuardado = true;
                $mostrarTicket = true;
                
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                sqlsrv_rollback($conn);
                $mensajeError = "Error al guardar el ticket: " . $e->getMessage();
            }
        }
    }
}

// Obtener todos los servicios
$sqlServicios = "SELECT idServicio, nombreServicio, preciosServicio FROM Servicio ORDER BY nombreServicio";
$stmtServicios = sqlsrv_query($conn, $sqlServicios);

// Obtener todos los medicamentos
$sqlMedicamentos = "SELECT idMedicamento, nombre, precioMedicamento FROM Medicamento ORDER BY nombre";
$stmtMedicamentos = sqlsrv_query($conn, $sqlMedicamentos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Tickets - Hospital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        :root {
            --color-text-heading: #1f2937;
            --color-text-body: #6b7280;
            --color-white: #ffffff;
            --color-black: #111827;
            --color-table-header-bg: #f3f4f6;
            --color-table-row-hover: #f9fafb;
            --color-primary: #3b82f6;
            --color-success: #10b981;
            --color-danger: #ef4444;
            --color-warning: #f59e0b;
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
            padding: 2rem 1rem;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: var(--color-white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px var(--shadow-light);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .header h1 {
            color: var(--color-text-heading);
            font-size: 2.5rem;
            margin: 0;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .form-section {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid #e2e8f0;
        }

        .form-section h3 {
            color: var(--color-text-heading);
            font-size: 1.25rem;
            margin: 0 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--color-text-heading);
            margin-bottom: 0.5rem;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color var(--transition-speed);
            box-sizing: border-box;
        }

        .form-group input[type="text"]:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .checkbox-group {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background: var(--color-white);
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color var(--transition-speed);
        }

        .checkbox-item:hover {
            background-color: var(--color-table-row-hover);
        }

        .checkbox-item:last-child {
            border-bottom: none;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 0.75rem;
            transform: scale(1.2);
        }

        .checkbox-item label {
            flex: 1;
            margin: 0;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-price {
            font-weight: 600;
            color: var(--color-success);
        }

        .ticket-section {
            grid-column: span 2;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 2rem;
            border-radius: var(--border-radius);
            border: 2px solid #d1d5db;
        }

        .ticket {
            background: var(--color-white);
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px var(--shadow-md);
            margin-bottom: 1rem;
        }

        .ticket-header {
            text-align: center;
            border-bottom: 2px solid var(--color-primary);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .hospital-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }

        .ticket-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .ticket-info strong {
            color: var(--color-text-heading);
        }

        .ticket-items {
            margin-bottom: 1.5rem;
        }

        .ticket-items h4 {
            color: var(--color-text-heading);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .ticket-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .ticket-item:last-child {
            border-bottom: none;
        }

        .ticket-total {
            border-top: 2px solid var(--color-primary);
            padding-top: 1rem;
            text-align: right;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-primary);
        }

        .btn {
            background-color: var(--color-black);
            color: var(--color-white);
            font-weight: 600;
            font-size: 1rem;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all var(--transition-speed);
            margin-right: 1rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background-color: #27272a;
            transform: translateY(-1px);
        }

        .btn-primary {
            background-color: var(--color-primary);
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-success {
            background-color: var(--color-success);
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .actions {
            text-align: center;
            margin-top: 1rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .alert-warning {
            background-color: #fef3c7;
            border: 1px solid var(--color-warning);
            color: #92400e;
        }

        .alert-danger {
            background-color: #fee2e2;
            border: 1px solid var(--color-danger);
            color: #991b1b;
        }

        .alert-success {
            background-color: #d1fae5;
            border: 1px solid var(--color-success);
            color: #065f46;
        }

        .no-print {
            /* Esta clase se usará para ocultar elementos al imprimir */
        }

        .success-badge {
            display: inline-block;
            background-color: var(--color-success);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .container { box-shadow: none; padding: 1rem; }
            .ticket { box-shadow: none; }
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .ticket-section {
                grid-column: span 1;
            }
            
            .ticket-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Generador de Tickets</h1>
            <p>Sistema de Facturación - Hospital General</p>
        </div>

        <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($mensajeError); ?>
        </div>
        <?php endif; ?>

        <?php if ($ticketGuardado && $mostrarTicket): ?>
        <div class="alert alert-success">
            <strong>✅ Éxito:</strong> El ticket ha sido guardado correctamente en la base de datos.
            <span class="success-badge">GUARDADO</span>
        </div>
        <?php endif; ?>

        <?php if (!$mostrarTicket): ?>
        <!-- Formulario para generar ticket -->
        <form method="POST" action="">
            <div class="content-grid">
                <!-- Datos del Paciente -->
                <div class="form-section">
                    <h3>📋 Datos del Paciente</h3>
                    <div class="form-group">
                        <label for="nombre_paciente">Nombre del Paciente *</label>
                        <input type="text" id="nombre_paciente" name="nombre_paciente" 
                               value="<?php echo htmlspecialchars($nombrePaciente); ?>" 
                               placeholder="Ingrese el nombre completo" required>
                    </div>
                    <div class="form-group">
                        <label for="doctor_responsable">Doctor Responsable</label>
                        <input type="text" id="doctor_responsable" name="doctor_responsable" 
                               value="<?php echo htmlspecialchars($doctorResponsable); ?>" 
                               placeholder="Nombre del médico">
                    </div>
                </div>

                <!-- Servicios -->
                <div class="form-section">
                    <h3>🩺 Servicios Médicos</h3>
                    <div class="checkbox-group">
                        <?php while ($servicio = sqlsrv_fetch_array($stmtServicios, SQLSRV_FETCH_ASSOC)): ?>
                        <div class="checkbox-item">
                            <input type="checkbox" 
                                   id="servicio_<?php echo $servicio['idServicio']; ?>" 
                                   name="servicios[]" 
                                   value="<?php echo $servicio['idServicio']; ?>">
                            <label for="servicio_<?php echo $servicio['idServicio']; ?>">
                                <span><?php echo htmlspecialchars($servicio['nombreServicio']); ?></span>
                                <span class="item-price">$<?php echo number_format($servicio['preciosServicio'], 2); ?></span>
                            </label>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Medicamentos -->
                <div class="form-section">
                    <h3>💊 Medicamentos</h3>
                    <div class="checkbox-group">
                        <?php while ($medicamento = sqlsrv_fetch_array($stmtMedicamentos, SQLSRV_FETCH_ASSOC)): ?>
                        <div class="checkbox-item">
                            <input type="checkbox" 
                                   id="medicamento_<?php echo $medicamento['idMedicamento']; ?>" 
                                   name="medicamentos[]" 
                                   value="<?php echo $medicamento['idMedicamento']; ?>">
                            <label for="medicamento_<?php echo $medicamento['idMedicamento']; ?>">
                                <span><?php echo htmlspecialchars($medicamento['nombre']); ?></span>
                                <span class="item-price">$<?php echo number_format($medicamento['precioMedicamento'], 2); ?></span>
                            </label>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">🎫 Generar y Guardar Ticket</button>
                <a href="recepcionista.php" class="btn">🔙 Volver al Panel</a>
            </div>
        </form>

        <?php else: ?>
        <!-- Mostrar Ticket -->
        <div class="ticket-section">
            <div class="ticket">
                <div class="ticket-header">
                    <div class="hospital-name">HOSPITAL GENERAL</div>
                    <div>Ticket de Servicios Médicos</div>
                </div>
                
                <div class="ticket-info">
                    <div><strong>Fecha:</strong> <?php echo date('d/m/Y'); ?></div>
                    <div><strong>Hora:</strong> <?php echo date('H:i:s'); ?></div>
                    <div><strong>Paciente:</strong> <?php echo htmlspecialchars($nombrePaciente); ?></div>
                    <div><strong>Doctor:</strong> <?php echo htmlspecialchars($doctorResponsable ?: 'No especificado'); ?></div>
                </div>

                <div class="ticket-items">
                    <h4>Servicios y Medicamentos:</h4>
                    
                    <?php if (!empty($serviciosSeleccionados)): ?>
                        <?php foreach ($serviciosSeleccionados as $servicio): ?>
                        <div class="ticket-item">
                            <span><?php echo htmlspecialchars($servicio['nombreServicio']); ?></span>
                            <span>$<?php echo number_format($servicio['preciosServicio'], 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($medicamentosSeleccionados)): ?>
                        <?php foreach ($medicamentosSeleccionados as $medicamento): ?>
                        <div class="ticket-item">
                            <span><?php echo htmlspecialchars($medicamento['nombre']); ?></span>
                            <span>$<?php echo number_format($medicamento['precioMedicamento'], 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="ticket-total">
                    <div><strong>TOTAL A PAGAR:</strong></div>
                    <div class="total-amount">$<?php echo number_format($total, 2); ?></div>
                </div>
            </div>

            <div class="actions no-print">
                <button onclick="window.print()" class="btn btn-success">🖨️ Imprimir Ticket</button>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary">📝 Nuevo Ticket</a>
                <a href="recepcionista.php" class="btn">🔙 Volver al Panel</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($mostrarTicket): ?>
    <script>
        // Auto-focus en el botón de imprimir cuando se muestra el ticket
        document.addEventListener('DOMContentLoaded', function() {
            // Opcionalmente, mostrar el diálogo de impresión automáticamente
            // window.print();
        });
    </script>
    <?php endif; ?>
</body>
</html>