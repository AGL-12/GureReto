<?php

/**
 * INICIO DE SESIÓN Y VERIFICACIÓN DE AUTENTICACIÓN
 */
// Inicia la sesión para acceder a datos del usuario autenticado
session_start();

/**
 * INCLUSIÓN DE LA CLASE Database
 * 
 * require_once: asegura que el archivo se incluya solo una vez.
 * __DIR__ . '/../config/Database.php': ruta absoluta desde este archivo.
 * Evita errores de rutas relativas.
 */
require_once __DIR__ . '/../config/Database.php';

/**
 * VERIFICAR QUE EL USUARIO ESTÉ AUTENTICADO
 * 
 * Si no existe 'user_id' en la sesión, el usuario no ha iniciado sesión.
 * Se responde con JSON de error y se detiene la ejecución.
 */
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No autenticado'  // Mensaje claro para el frontend
    ]);
    exit; // Termina el script inmediatamente
}

/**
 * VERIFICAR AUTORIZACIÓN: SOLO ADMINISTRADORES
 * 
 * Se asume que en el login se guardó $_SESSION['tipo'] = 'admin' o 'usuario'
 * Si no es admin, se deniega el acceso.
 */
if ($_SESSION['tipo'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Acceso denegado'  // Solo admins pueden ver la lista
    ]);
    exit;
}

/**
 * BLOQUE TRY-CATCH
 * 
 * Captura cualquier error (conexión, consulta, PDO, etc.)
 * Evita que el usuario vea errores internos del servidor.
 */
try {

    /**
     * CREAR INSTANCIA DE Database Y OBTENER CONEXIÓN
     */
    $db = new Database();                    // Nueva instancia de la clase
    $conn = $db->getConnection();            // Objeto PDO configurado y listo

    /**
     * CONSULTA SQL PARA LISTAR TODOS LOS USUARIOS
     * 
     * Usa INNER JOIN: solo usuarios que tengan perfil y datos en 'usuario'
     * Selecciona campos necesarios (sin contraseña)
     * ORDER BY: ordena por apellido y nombre para mejor UX
     */
    $stmt = $conn->prepare("
        SELECT 
            p.id, 
            p.email, 
            p.nombre, 
            p.apellido, 
            p.telefono, 
            u.genero
        FROM perfil p
        JOIN usuario u ON p.id = u.id
        ORDER BY p.apellido ASC, p.nombre ASC
    ");

    /**
     * EJECUTAR LA CONSULTA
     * 
     * No hay parámetros → no se necesita bindParam()
     * La consulta es estática y segura.
     */
    $stmt->execute();

    /**
     * OBTENER TODOS LOS RESULTADOS
     * 
     * fetchAll(PDO::FETCH_ASSOC): devuelve un array de arrays asociativos
     * Ejemplo: $usuarios[0]['nombre'] = 'Ana'
     */
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /**
     * RESPUESTA EXITOSA EN JSON
     * 
     * Estructura clara para el frontend:
     * - success: true
     * - usuarios: array con todos los perfiles
     */
    echo json_encode([
        'success' => true,
        'usuarios' => $usuarios
    ], JSON_UNESCAPED_UNICODE);  // Soporta acentos y ñ sin codificarlos

} catch (Exception $e) {
    /**
     * MANEJO DE ERRORES EN PRODUCCIÓN
     * 
     * NO se muestra $e->getMessage() al usuario final.
     * Solo se registra en logs (opcional con error_log()).
     * Mensaje genérico para evitar exposición de información sensible.
     */
    // Opcional: registrar error en archivo de logs
    error_log("Error en lista_usuarios.php: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor'  // Mensaje seguro
    ]);
}

?>