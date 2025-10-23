<?php
// REGISTER - SOLO USUARIOS
    require_once '../controller/Control.php';
    require_once __DIR__ . '/../config/Database.php';
    require_once __DIR__ . '/../models/Perfil.php';
    require_once __DIR__ . '/../models/Usuario.php';
    require_once __DIR__ . '/../models/Admin.php';
    
        $email = $_POST['email'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $apellido = $_POST['apellido'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $genero = $_POST['genero'] ?? '';
        
        // Validar que el email no exista
        $query_check = "SELECT id FROM perfil WHERE email = :email";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();
        
        if($stmt_check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
            return;
        }
        
        // Crear objeto Perfil
        $perfil = new Perfil(null, $email, $contrasena, $nombre, $apellido, $telefono);
        
        // SQL INSERT en perfil
        $query = "INSERT INTO perfil (email, contrasena, nombre, apellido, telefono) 
                  VALUES (:email, :contrasena, :nombre, :apellido, :telefono)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $perfil->getEmail());
        $stmt->bindParam(':contrasena', $perfil->getContrasena());
        $stmt->bindParam(':nombre', $perfil->getNombre());
        $stmt->bindParam(':apellido', $perfil->getApellido());
        $stmt->bindParam(':telefono', $perfil->getTelefono());
        
        if($stmt->execute()) {
            $perfil_id = $this->conn->lastInsertId();
            
            // SIEMPRE crear como usuario normal
            $usuario = new Usuario($perfil_id, $genero);
            $query_usuario = "INSERT INTO usuario (id, genero) VALUES (:id, :genero)";
            $stmt_usuario = $this->conn->prepare($query_usuario);
            $stmt_usuario->bindParam(':id', $usuario->getId());
            $stmt_usuario->bindParam(':genero', $usuario->getGenero());
            
            if($stmt_usuario->execute()) {
                echo json_encode(['success' => true, 'message' => 'Registro exitoso']);
            } else {
                // Si falla la inserción en usuario, eliminar el perfil creado
                $query_delete = "DELETE FROM perfil WHERE id = :id";
                $stmt_delete = $this->conn->prepare($query_delete);
                $stmt_delete->bindParam(':id', $perfil_id);
                $stmt_delete->execute();
                
                echo json_encode(['success' => false, 'message' => 'Error al crear usuario']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar']);
        }
    ?>