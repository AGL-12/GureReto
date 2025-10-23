<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'controller/Control.php';

$action = $_GET['action'] ?? '';
$controller = new Control();

switch($action) {
    case 'login':
        include __DIR__ . '/api/iniciarSesion.php';
        break;
        
    case 'register':
        include __DIR__ . '/api/registrar.php';
        break;
        
    case 'logout':
        include __DIR__ . '/api/cerrarSesion.php';
        break;
    
    // Gestión de usuarios
    case 'listar':
        break;
        
    case 'obtener':
        break;
        
    case 'crear':
        break;
        
    case 'actualizar':
        break;
        
    case 'eliminar':
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
?>
