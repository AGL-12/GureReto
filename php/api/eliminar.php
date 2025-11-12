<?php

/**
 * INCLUSIÓN DE LA CLASE Database
 * 
 * require_once: incluye el archivo solo una vez.
 * __DIR__ . '/../config/Database.php': ruta absoluta desde este archivo hacia la carpeta config.
 * Esto evita errores de rutas relativas.
 */
require_once __DIR__ . '/../config/Database.php';

/**
 * BLOQUE TRY-CATCH
 * 
 * Captura cualquier excepción (de PDO, errores de conexión, etc.)
 * para evitar que el usuario vea errores internos del servidor.
 */
try {
    
    /**
     * CREAR INSTANCIA DE LA CLASE Database Y OBTENER CONEXIÓN
     */
    $db = new Database();                    // Instancia el objeto Database
    $conn = $db->getConnection();            // Obtiene el objeto PDO listo para usar

    /**
     * OBTENER Y VALIDAR EL ID DEL USUARIO DESDE LA URL (GET)
     * 
     * isset($_GET['id']) ? ... : 0 → Si no existe, asigna 0
     * intval() → Convierte a entero (evita inyección tipo "1 OR 1=1")
     */
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /**
     * VALIDACIÓN DE ID VÁLIDO
     * 
     * Si el ID es menor o igual a 0, se considera inválido.
     * Se responde con JSON y se detiene la ejecución.
     */
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de usuario no válido.'
        ]);
        exit; // Termina el script
    }

    /**
     * CONSULTA PREPARADA PARA ELIMINAR USUARIO
     * 
     * Usa :id como parámetro nombrado → evita inyección SQL.
     */
    $stmt = $conn->prepare("DELETE FROM perfil WHERE id = :id");

    /**
     * VINCULAR PARÁMETRO CON TIPO EXPLÍCITO
     * 
     * PDO::PARAM_INT → fuerza que el valor sea entero.
     * Muy importante para seguridad.
     */
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    /**
     * EJECUTAR LA ELIMINACIÓN Y VERIFICAR RESULTADO
     */
    if ($stmt->execute()) {
        // execute() devuelve true si la consulta se ejecutó sin errores
        echo json_encode([
            'success' => true,
            'message' => 'Usuario eliminado correctamente.'
        ]);
    } else {
        // Si hay error (por ejemplo, restricción de clave foránea)
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo eliminar el usuario.'
        ]);
    }

} catch (Exception $e) {
    /**
     * CAPTURA DE ERRORES GENERALES
     * 
     * Incluye errores de conexión, PDO, sintaxis, etc.
     * En producción, NO muestres $e->getMessage() al usuario final.
     */
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud.',
        'error' => $e->getMessage()  // ¡Quitar en producción!
    ]);
}

?>