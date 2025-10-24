<?php
require_once __DIR__ . '/../config/Database.php';;
try {
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("
        SELECT p.id, p.email, p.nombre, p.apellido, p.telefono, u.genero
        FROM perfil p
        JOIN usuario u ON p.id = u.id
    ");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'usuarios' => $usuarios]);
} catch (Exception $e) {
    // Captura cualquier error (incluyendo fallos de conexión lanzados por Database.php)
    // 4. Devolver JSON de error válido
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud.',
        'error' => $e->getMessage()
    ]);
}
?>