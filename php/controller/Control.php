<?php
// control.php - punto de entrada central
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Responder preflight CORS
    http_response_code(200);
    exit;
}

// Rutas robustas usando __DIR__ (directorio donde está este archivo)
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Perfil.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Admin.php';


// Clase Control, se define antes de usarla para evitar problemas
class Control
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

// Obtener la acción enviada por POST (o GET como fallback)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'No se recibió ninguna acción']);
    exit;
}

// Crear instancia por si se usa dentro de las APIs
$control = new Control();

// Ruteo a los scripts dentro de /api según la acción.
// Usamos rutas absolutas relativas a este archivo (__DIR__) para evitar errores.
switch ($action) {
    case 'login':
        // incluir el archivo que procesa el login
        // el archivo incluido debe emitir JSON (echo json_encode(...))
        require_once __DIR__ . '/../api/iniciarSesion.php';
        break;

    case 'register':
        require_once __DIR__ . '/../api/registrar.php';
        break;

    case 'logout':
        require_once __DIR__ . '/../api/cerrarSesion.php';
        break;
    case 'listar':
        require_once __DIR__ . '/../api/cargarTabla.php';
        break;
    case 'borrar':
        require_once __DIR__ . '/../api/eliminar.php';
        break;

    case 'obtenerSesion':
        require_once __DIR__ . '/../api/obtenerSesion.php';
        break;

    case 'actualizarPerfil':
        require_once __DIR__ . '/../api/actualizarPerfil.php';
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
