<?php

/**
 * Clase Database
 * 
 * Esta clase se encarga de establecer y gestionar la conexión a la base de datos
 * utilizando PDO (PHP Data Objects), que es una extensión segura y flexible para
 * acceder a bases de datos en PHP.
 */
class Database {

    // Propiedades privadas: solo accesibles dentro de la clase
    private $host = 'localhost';      // Dirección del servidor de base de datos (generalmente localhost en desarrollo)
    private $db_name = 'perfiles';    // Nombre de la base de datos a la que se conectará
    private $username = 'root';       // Usuario de la base de datos (en desarrollo suele ser 'root')
    private $password = 'abcd*1234';  // Contraseña del usuario de la base de datos
    private $conn;                    // Almacenará el objeto de conexión PDO una vez creada

    /**
     * Método público: getConnection()
     * 
     * Este método crea y devuelve una conexión a la base de datos.
     * Si hay error, lo muestra. Si todo va bien, retorna el objeto PDO listo para usar.
     */
    public function getConnection() {
        
        // Inicializamos la conexión como null para evitar conexiones previas no cerradas
        $this->conn = null;

        try {
            // Creamos una nueva instancia de PDO
            // Cadena de conexión: mysql:host=localhost;dbname=perfiles
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            // Configuramos PDO para que lance excepciones cuando ocurra un error
            // Esto facilita el manejo de errores con try-catch
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Establecemos la codificación de caracteres a UTF-8
            // Esto asegura que los datos con acentos, ñ, emojis, etc., se manejen correctamente
            $this->conn->exec("set names utf8");

        } catch(PDOException $e) {
            // Si falla la conexión (servidor caído, credenciales malas, BD no existe, etc.)
            // Mostramos un mensaje claro con el error específico
            echo "Error de conexión: " . $e->getMessage();
        }

        // Devolvemos el objeto de conexión (puede ser null si falló)
        return $this->conn;
    }
}

?>