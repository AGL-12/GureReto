<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/Perfil.php';
class PerfilModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function LoginPerfil($email, $password)
    {
        $query = "SELECT * FROM perfiles WHERE email = :email AND contrasena = :contrasena";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contrasena', $password);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Verificamos si es un usuario o un admin
            // Buscar en la tabla 'usuario'
            $queryUsuario = "SELECT * FROM usuario WHERE id = :id";
            $stmtUsuario = $this->conn->prepare($queryUsuario);
            $stmtUsuario->bindParam(':id', $row['id']);
            $stmtUsuario->execute();
            $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

            // Si existe en la tabla 'usuario', es un usuario
            if ($usuario) {
                return new Usuario($row['id'], $row['email'], $row['contrasena'], $row['nombre'], $row['apellido'], $row['telefono'], $usuario['genero']);
            }

            // Si no existe en la tabla 'usuario', verificamos si es un admin
            $queryAdmin = "SELECT * FROM admin WHERE id = :id";
            $stmtAdmin = $this->conn->prepare($queryAdmin);
            $stmtAdmin->bindParam(':id', $row['id']);
            $stmtAdmin->execute();
            $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

            // Si existe en la tabla 'admin', es un admin
            if ($admin) {
                return new Admin($row['id'], $row['email'], $row['contrasena'], $row['nombre'], $row['apellido'], $row['telefono'], $admin['cuenta_corriente']);
            }
        } else {
            return null;
        }
    }
}
