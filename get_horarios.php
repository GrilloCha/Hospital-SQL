<?php
require 'conexion.php';

if (!isset($_GET['idDoctor']) || !isset($_GET['fecha'])) {
    echo json_encode([]);
    exit();
}

$idDoctor = intval($_GET['idDoctor']);
$fecha = $_GET['fecha'];

// Define horarios posibles (ejemplo: 9am a 5pm cada hora)
$horariosPosibles = [
    '09:00', '10:00', '11:00', '12:00',
    '13:00', '14:00', '15:00', '16:00'
];

// ✅ Obtener citas ya agendadas (de la columna horaCita, no fechaCita)
$sql = "SELECT CONVERT(VARCHAR(5), horaCita, 108) as hora FROM Cita WHERE idDoctor = ? AND CONVERT(date, fechaCita) = ?";
$params = [$idDoctor, $fecha];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode([]);
    exit();
}

$horariosOcupados = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $horariosOcupados[] = $row['hora'];
}

// Filtrar horarios disponibles
$horariosDisponibles = array_filter($horariosPosibles, function ($hora) use ($horariosOcupados) {
    return !in_array($hora, $horariosOcupados);
});

header('Content-Type: application/json');
echo json_encode(array_values($horariosDisponibles));
