<?php
require 'conexion.php';

$idTicket = $_GET['id'];
$sql = "SELECT T.fecha, T.nombreUsuario, S.nombre AS servicio, S.precio AS precioServicio,
               M.nombre AS medicamento, M.precio AS precioMed,
               P.montoTotal
        FROM Ticket T
        JOIN Servicio S ON T.idServicio = S.idServicio
        LEFT JOIN Medicamento M ON T.idMedicamento = M.idMedicamento
        JOIN PagoTicket P ON T.idTicket = P.idTicket
        WHERE T.idTicket = ?";

$stmt = sqlsrv_query($conn, $sql, [$idTicket]);
$datos = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
?>

<h2>Ticket #<?= $idTicket ?></h2>
<p><strong>Fecha:</strong> <?= $datos['fecha']->format('Y-m-d H:i') ?></p>
<p><strong>Recepcionista:</strong> <?= $datos['nombreUsuario'] ?></p>
<p><strong>Servicio:</strong> <?= $datos['servicio'] ?> ($<?= $datos['precioServicio'] ?>)</p>
<?php if ($datos['medicamento']): ?>
<p><strong>Medicamento:</strong> <?= $datos['medicamento'] ?> ($<?= $datos['precioMed'] ?>)</p>
<?php endif; ?>
<p><strong>Total:</strong> $<?= $datos['montoTotal'] ?></p>
