<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    exit;
}

$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$telefono = $_POST['telefono'] ?? '';

$conn = $control->getConnection();

$query = "UPDATE perfil SET nombre = :nombre, apellido = :apellido, telefono = :telefono WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':apellido', $apellido);
$stmt->bindParam(':telefono', $telefono);
$stmt->bindParam(':id', $_SESSION['user_id']);

if ($stmt->execute()) {
    $_SESSION['nombre'] = $nombre; // Actualizar sesión
    echo json_encode(['success' => true, 'nombre' => $nombre, 'message' => 'Perfil actualizado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
}
?>