<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controller/Control.php';

$database = new Database();
$conn = $database->getConnection();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$control = new Control();
$perfil = $control->login($email, $password);

if ($perfil) {
    $_SESSION['user_id'] = $perfil->getId();
    $_SESSION['email'] = $perfil->getEmail();
    $_SESSION['nombre'] = $perfil->getNombre();

    if ($perfil instanceof Admin) {
        $_SESSION['tipo'] = 'admin';
    } else {
        $_SESSION['tipo'] = 'usuario';
    }

    echo json_encode([
        'success' => true,
        'tipo' => $_SESSION['tipo'],
        'nombre' => $_SESSION['nombre']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    exit();
}
