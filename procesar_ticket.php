<?php
require 'conexion.php';
session_start();

$idServicio = intval($_POST['idServicio']);
$idMedicamento = !empty($_POST['idMedicamento']) ? intval($_POST['idMedicamento']) : null;
$nombreRecepcionista = $_SESSION['usuario'] ?? 'Desconocido';

// Obtener precios
$sqlServicio = "SELECT precio FROM Servicio WHERE idServicio = ?";
$stmtS = sqlsrv_query($conn, $sqlServicio, [$idServicio]);
$precioServicio = sqlsrv_fetch_array($stmtS, SQLSRV_FETCH_ASSOC)['precio'];

$precioMedicamento = 0;
if ($idMedicamento) {
    $sqlMed = "SELECT precio FROM Medicamento WHERE idMedicamento = ?";
    $stmtM = sqlsrv_query($conn, $sqlMed, [$idMedicamento]);
    $precioMedicamento = sqlsrv_fetch_array($stmtM, SQLSRV_FETCH_ASSOC)['precio'];
}

$montoTotal = $precioServicio + $precioMedicamento;

// Insertar ticket
$sqlTicket = "INSERT INTO Ticket (fecha, nombreUsuario, idServicio, idMedicamento)
              VALUES (GETDATE(), ?, ?, ?)";
$paramsTicket = [$nombreRecepcionista, $idServicio, $idMedicamento];
sqlsrv_query($conn, $sqlTicket, $paramsTicket);

// Obtener id del ticket
$stmtID = sqlsrv_query($conn, "SELECT SCOPE_IDENTITY() AS idTicket");
sqlsrv_fetch($stmtID);
$idTicket = sqlsrv_get_field($stmtID, 0);

// Insertar pago
$sqlPago = "INSERT INTO PagoTicket (idTicket, montoTotal) VALUES (?, ?)";
sqlsrv_query($conn, $sqlPago, [$idTicket, $montoTotal]);

header("Location: confirmar_ticket.php?id=$idTicket");
exit();
