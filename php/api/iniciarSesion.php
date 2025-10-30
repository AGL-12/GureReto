<?php
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
}
?>