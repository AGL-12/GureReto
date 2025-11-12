<?php

/**
 * INICIO DE SESIÓN Y VERIFICACIÓN DE AUTENTICACIÓN
 */
// Inicia o reanuda la sesión del usuario.
// Esto permite acceder a variables como $_SESSION['user_id']
session_start();

/**
 * VERIFICACIÓN DE SESIÓN ACTIVA
 * 
 * Si no existe 'user_id' en la sesión, significa que el usuario no está autenticado.
 * Se responde con JSON de error y se detiene la ejecución con exit.
 */
if (!isset($_SESSION['user_id'])) {
    // Respuesta en formato JSON: indica que no hay sesión
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    exit; // Termina el script inmediatamente
}

/**
 * OBTENER CONEXIÓN A LA BASE DE DATOS
 * 
 * Se asume que existe un objeto $control (probablemente una instancia de una clase como 'Controller')
 * que tiene el método getConnection() (como el de la clase Database anterior).
 */
$conn = $control->getConnection();

/**
 * CONSULTA SQL PREPARADA (SEGURA CONTRA INYECCIÓN SQL)
 * 
 * Selecciona todos los campos del perfil (p.*) y el género del usuario (u.genero)
 * Une las tablas 'perfil' y 'usuario' mediante LEFT JOIN para obtener datos completos.
 * Filtra por el ID del usuario autenticado usando un parámetro nombrado :id
 */
$query = "SELECT p.*, u.genero 
          FROM perfil p 
          LEFT JOIN usuario u ON p.id = u.id 
          WHERE p.id = :id";

/**
 * PREPARAR Y EJECUTAR LA CONSULTA
 */
// Prepara la consulta (evita inyección SQL)
$stmt = $conn->prepare($query);

// Vincula el ID de sesión al parámetro :id de forma segura
$stmt->bindParam(':id', $_SESSION['user_id']);

// Ejecuta la consulta preparada
$stmt->execute();

/**
 * OBTENER RESULTADO
 * 
 * fetch(PDO::FETCH_ASSOC) devuelve la fila como un array asociativo:
 * ['id' => 1, 'nombre' => 'Juan', ...]
 */
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

/**
 * RESPUESTA EN JSON SEGÚN EL RESULTADO
 */
if ($usuario) {
    // ÉXITO: usuario encontrado
    echo json_encode([
        'success' => true,
        'id' => $usuario['id'],
        'nombre' => $usuario['nombre'],
        'apellido' => $usuario['apellido'],
        'email' => $usuario['email'],
        'telefono' => $usuario['telefono'],
        'genero' => $usuario['genero']  // Viene de la tabla 'usuario'
    ]);
} else {
    // ERROR: no se encontró el perfil (aunque el ID exista en sesión)
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

?>