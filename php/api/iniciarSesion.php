<?php
// Este archivo es incluido desde control.php
// Ya NO necesita require_once porque control.php ya los cargó

<<<<<<< HEAD
header('Content-Type: application/json');
session_start();

$database = new Database();
$conn = $database->getConnection();

$email = $_POST['email'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

if (empty($email) || empty($contrasena)) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

try {
    // ⚠️ Asegúrate de usar el nombre correcto de la columna (correo o email)
    $query = "SELECT * FROM perfil WHERE email = :email AND contrasena = :contrasena";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contrasena', $contrasena);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $perfil = new Perfil(
            $resultado['id'],
            $resultado['correo'], // cambia aquí si el campo es distinto
            $resultado['contrasena'],
            $resultado['nombre'],
            $resultado['apellido'],
            $resultado['telefono']
        );

        $query_admin = "SELECT * FROM admin WHERE id = :id";
        $stmt_admin = $conn->prepare($query_admin);
        $stmt_admin->bindParam(':id', $perfil->getId());
        $stmt_admin->execute();
        $es_admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user_id'] = $perfil->getId();
        $_SESSION['email'] = $perfil->getEmail();
        $_SESSION['nombre'] = $perfil->getNombre();
        $_SESSION['tipo'] = $es_admin ? 'admin' : 'usuario';

        echo json_encode([
            'success' => true,
            'tipo' => $_SESSION['tipo'],
            'nombre' => $_SESSION['nombre']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
=======
session_start();

$email = $_POST['email'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

// Obtener la conexión del objeto $control que ya existe
$conn = $control->getConnection();

// SQL para buscar el perfil
$query = "SELECT * FROM perfil WHERE email = :email AND contrasena = :contrasena";
$stmt = $conn->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':contrasena', $contrasena);
$stmt->execute();

$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

if($resultado) {
    // Crear objeto Perfil con los datos
    $perfil = new Perfil(
        $resultado['id'],
        $resultado['email'],
        $resultado['contrasena'],
        $resultado['nombre'],
        $resultado['apellido'],
        $resultado['telefono']
    );
    
    // Verificar si es admin
    $query_admin = "SELECT * FROM admin WHERE id = :id";
    $stmt_admin = $conn->prepare($query_admin);
    $stmt_admin->bindParam(':id', $resultado['id']);
    $stmt_admin->execute();
    $es_admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);
    
    // Guardar sesión
    $_SESSION['user_id'] = $perfil->getId();
    $_SESSION['email'] = $perfil->getEmail();
    $_SESSION['nombre'] = $perfil->getNombre();
    $_SESSION['tipo'] = $es_admin ? 'admin' : 'usuario';
    
    echo json_encode([
        'success' => true,
        'tipo' => $_SESSION['tipo'],
        'nombre' => $perfil->getNombre()
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Credenciales incorrectas'
    ]);
>>>>>>> 5cc328adc7fd7ac2e6c9b32162e1d6f5f780079c
}
?>