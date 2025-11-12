<?php
// api/registrar.php

/**
 * ESTE ARCHIVO SE INCLUYE DESDE control.php
 * $control ya existe y tiene la conexión
 */

// Verificar que $control exista (seguridad extra)
if (!isset($control) || !method_exists($control, 'getConnection')) {
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor.'
    ]);
    exit;
}

// Usar la conexión del $control
$conn = $control->getConnection();

/**
 * OBTENER Y LIMPIAR DATOS DEL FORMULARIO
 */
$email     = trim($_POST['Email'] ?? '');
$password  = $_POST['Password'] ?? '';
$nombre    = trim($_POST['FullName'] ?? '');
$apellido  = trim($_POST['LastName'] ?? '');
$telefono  = trim($_POST['Phone'] ?? '');
$genero    = strtoupper(trim($_POST['Gender'] ?? ''));

/**
 * VALIDACIÓN DE CAMPOS
 */
$errores = [];

if (empty($email)) {
    $errores['Email'] = 'El email es obligatorio.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores['Email'] = 'Email inválido.';
}

if (empty($password)) {
    $errores['Password'] = 'La contraseña es obligatoria.';
} elseif (strlen($password) < 6) {
    $errores['Password'] = 'Mínimo 6 caracteres.';
}

if (empty($nombre))    $errores['FullName'] = 'El nombre es obligatorio.';
if (empty($apellido))  $errores['LastName'] = 'El apellido es obligatorio.';

if (empty($telefono)) {
    $errores['Phone'] = 'El teléfono es obligatorio.';
} elseif (!preg_match('/^\d{9,15}$/', $telefono)) {
    $errores['Phone'] = 'Teléfono: 9-15 dígitos.';
}

if (empty($genero)) {
    $errores['Gender'] = 'El género es obligatorio.';
} elseif (!in_array(strtolower($genero), ['masculino', 'femenino', 'otro'])) {
    $errores['Gender'] = 'Género: Masculino, Femenino u Otro.';
}


if (!empty($errores)) {
    echo json_encode([
        'success' => false,
        'errores' => $errores
    ]);
    exit;
}

/**
 * VERIFICAR EMAIL ÚNICO
 */
try {
    $check = $conn->prepare("SELECT id FROM perfil WHERE email = :email");
    $check->bindParam(':email', $email);
    $check->execute();

    if ($check->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'errores' => ['Email' => 'Este email ya está registrado.']
        ]);
        exit;
    }

    $hash_contrasena = $password;

    /**
     * TRANSACCIÓN: perfil + usuario
     */
    $conn->beginTransaction();

    // 1. Insertar en perfil
    $stmt_perfil = $conn->prepare("
        INSERT INTO perfil (email, contrasena, nombre, apellido, telefono)
        VALUES (:email, :contrasena, :nombre, :apellido, :telefono)
    ");
    $stmt_perfil->bindParam(':email', $email);
    $stmt_perfil->bindParam(':contrasena', $hash_contrasena);
    $stmt_perfil->bindParam(':nombre', $nombre);
    $stmt_perfil->bindParam(':apellido', $apellido);
    $stmt_perfil->bindParam(':telefono', $telefono);

    if (!$stmt_perfil->execute()) {
        throw new Exception('Error al insertar perfil');
    }

    $user_id = $conn->lastInsertId();

    // 2. Insertar en usuario (género)
    $stmt_usuario = $conn->prepare("
        INSERT INTO usuario (id, genero) VALUES (:id, :genero)
    ");
    $stmt_usuario->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt_usuario->bindParam(':genero', $genero);

    if (!$stmt_usuario->execute()) {
        throw new Exception('Error al insertar género');
    }

    $conn->commit();

    /**
     * ÉXITO
     * redirect: ruta RELATIVA al archivo HTML que llama (registro.html)
     */
    echo json_encode([
        'success' => true,
        'message' => '¡Registro exitoso! Puedes iniciar sesión.',
        'redirect' => 'Login.html'  // ¡CORREGIDO! (mismo directorio)
    ]);

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Error en registro: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor. Inténtalo más tarde.'
    ]);
}
?>