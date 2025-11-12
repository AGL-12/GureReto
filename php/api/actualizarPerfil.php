<?php

/**
 * INICIO DE SESIÓN Y VERIFICACIÓN DE AUTENTICACIÓN
 */
// Inicia o reanuda la sesión para acceder a $_SESSION
session_start();

/**
 * VERIFICAR QUE EL USUARIO ESTÉ AUTENTICADO
 * 
 * Si no existe 'user_id' en la sesión, el usuario no ha iniciado sesión.
 * Se responde con JSON de error y se detiene el script.
 */
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No hay sesión activa'
    ]);
    exit; // Termina ejecución inmediatamente
}

/**
 * OBTENER DATOS DEL FORMULARIO (POST)
 * 
 * Usa el operador null coalescing (??) para evitar errores si los campos no existen.
 * Si no se envía un campo, se asigna cadena vacía.
 */
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$telefono = $_POST['telefono'] ?? '';

/**
 * CONEXIÓN A LA BASE DE DATOS
 * 
 * Se asume que $control es un objeto global o instanciado previamente
 * con el método getConnection() (como la clase Database).
 */
$conn = $control->getConnection();

/**
 * CONSULTA SQL PREPARADA PARA ACTUALIZAR PERFIL
 * 
 * Usa parámetros nombrados (:nombre, :apellido, etc.) → evita inyección SQL.
 * Actualiza solo los campos permitidos en la tabla 'perfil'.
 * Filtra por ID del usuario autenticado.
 */
$query = "UPDATE perfil 
          SET nombre = :nombre, 
              apellido = :apellido, 
              telefono = :telefono 
          WHERE id = :id";

/**
 * PREPARAR LA CONSULTA
 * 
 * prepare() separa la consulta del dato → seguridad contra inyección.
 */
$stmt = $conn->prepare($query);

/**
 * VINCULAR PARÁMETROS DE FORMA SEGURA
 * 
 * bindParam() asocia cada :parametro con una variable PHP.
 * PDO escapa automáticamente los valores.
 */
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':apellido', $apellido);
$stmt->bindParam(':telefono', $telefono);
$stmt->bindParam(':id', $_SESSION['user_id']); // Solo actualiza su propio perfil

/**
 * EJECUTAR LA ACTUALIZACIÓN Y VERIFICAR RESULTADO
 */
if ($stmt->execute()) {
    /**
     * ÉXITO: perfil actualizado
     * 
     * También se actualiza $_SESSION['nombre'] para que el frontend
     * muestre el nombre nuevo sin recargar.
     */
    $_SESSION['nombre'] = $nombre;

    echo json_encode([
        'success' => true,
        'nombre' => $nombre,
        'message' => 'Perfil actualizado'
    ]);
} else {
    /**
     * ERROR: no se pudo ejecutar (restricción, BD caída, etc.)
     * 
     * execute() devuelve false si hay error.
     */
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar'
    ]);
}

?>