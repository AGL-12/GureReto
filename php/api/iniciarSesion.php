<?php

/**
 * INICIO DE SESIÓN Y RECEPCIÓN DE DATOS
 */
// Inicia la sesión para poder usar $_SESSION más adelante
session_start();

/**
 * OBTENER DATOS DEL FORMULARIO
 * 
 * Usa el operador null coalescing (??) para evitar errores si los campos no existen.
 * Si no se envía 'email' o 'contrasena', se asigna cadena vacía.
 */
$email = $_POST['email'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

/**
 * CONEXIÓN A LA BASE DE DATOS
 * 
 * Se asume que $control es un objeto global o instanciado previamente
 * que tiene el método getConnection() (como la clase Database que vimos antes).
 */
$conn = $control->getConnection();

/**
 * CONSULTA PARA BUSCAR USUARIO POR EMAIL Y CONTRASEÑA
 * 
 * ¡ADVERTENCIA! Aquí se compara la contraseña en texto plano.
 * Esto es MUY INSEGURO si las contraseñas no están hasheadas.
 */
$query = "SELECT * FROM perfil WHERE email = :email AND contrasena = :contrasena";

// Prepara la consulta (evita inyección SQL)
$stmt = $conn->prepare($query);

// Vincula los parámetros de forma segura
$stmt->bindParam(':email', $email);
$stmt->bindParam(':contrasena', $contrasena);

// Ejecuta la consulta
$stmt->execute();

// Obtiene la primera fila como array asociativo
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

/**
 * VERIFICAR SI LAS CREDENCIALES SON CORRECTAS
 */
if ($resultado) {
    
    /**
     * CREAR OBJETO Perfil (se asume que existe una clase Perfil con constructor y getters)
     */
    $perfil = new Perfil(
        $resultado['id'],
        $resultado['email'],
        $resultado['contrasena'],  // ¡Nunca guardes la contraseña en el objeto!
        $resultado['nombre'],
        $resultado['apellido'],
        $resultado['telefono']
    );
    
    /**
     * VERIFICAR SI EL USUARIO ES ADMINISTRADOR
     * 
     * Busca en la tabla 'admin' si el ID del usuario está registrado como admin.
     */
    $query_admin = "SELECT * FROM admin WHERE id = :id";
    $stmt_admin = $conn->prepare($query_admin);
    $stmt_admin->bindParam(':id', $resultado['id']);
    $stmt_admin->execute();
    $es_admin = $stmt_admin->fetch(PDO::FETCH_ASSOC); // Devuelve fila si existe, false si no
    
    /**
     * GUARDAR DATOS EN LA SESIÓN
     * 
     * Se guardan datos básicos del usuario para usarlo en otras páginas.
     * 'tipo' será 'admin' o 'usuario'
     */
    $_SESSION['user_id'] = $perfil->getId();
    $_SESSION['email'] = $perfil->getEmail();
    $_SESSION['nombre'] = $perfil->getNombre();
    $_SESSION['tipo'] = $es_admin ? 'admin' : 'usuario'; // Operador ternario
    
    /**
     * RESPUESTA EXITOSA EN JSON
     */
    echo json_encode([
        'success' => true,
        'tipo' => $_SESSION['tipo'],
        'nombre' => $perfil->getNombre()
    ]);

} else {
    /**
     * RESPUESTA FALLIDA: credenciales incorrectas
     */
    echo json_encode([
        'success' => false,
        'message' => 'Credenciales incorrectas'
    ]);
}

?>