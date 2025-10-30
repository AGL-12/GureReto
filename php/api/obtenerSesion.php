<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    exit;
}

// Obtener conexión
$conn = $control->getConnection();

// Consultar datos completos del usuario
$query = "SELECT p.*, u.genero 
          FROM perfil p 
          LEFT JOIN usuario u ON p.id = u.id 
          WHERE p.id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    echo json_encode([
        'success' => true,
        'id' => $usuario['id'],
        'nombre' => $usuario['nombre'],
        'apellido' => $usuario['apellido'],
        'email' => $usuario['email'],
        'telefono' => $usuario['telefono'],
        'genero' => $usuario['genero']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}
?>