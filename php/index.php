<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'controller/Control.php';

$action = $_GET['action'] ?? '';
$controller = new Control();

switch($action) {
    // Autenticación
    case 'login':
        $controller->login();
        break;
        
    case 'register':
        $controller->register();
        break;
        
    case 'logout':
        $controller->logout();
        break;
    
    // Gestión de usuarios
    case 'listar':
        $controller->listar();
        break;
        
    case 'obtener':
        $controller->obtenerPerfil();
        break;
        
    case 'crear':
        $controller->crear();
        break;
        
    case 'actualizar':
        $controller->actualizar();
        break;
        
    case 'eliminar':
        $controller->eliminar();
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
?>