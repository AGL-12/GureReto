<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');

// Rutas relativas desde este archivo
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Perfil.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Admin.php';

class Control {
    private $conn;

    public function __construct() {
        try {
            $database = new Database();
            $this->conn = $database->getConnection();
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    public function login($email, $password) {
        try {
            // Login admin
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $adminData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($adminData && password_verify($password, $adminData['contrasena'])) {
                echo json_encode([
                    'success' => true,
                    'tipo' => 'admin',
                    'message' => 'Login exitoso'
                ]);
                exit;
            }

            // Login usuario
            $stmt2 = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
            $stmt2->execute([$email]);
            $userData = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($userData && password_verify($password, $userData['contrasena'])) {
                echo json_encode([
                    'success' => true,
                    'tipo' => 'usuario',
                    'message' => 'Login exitoso'
                ]);
                exit;
            }

            echo json_encode([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ]);
        }
    }
}

// Solo manejar login si se hace POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['contrasena'] ?? '';

    $control = new Control();
    $control->login($email, $password);
}
?>
