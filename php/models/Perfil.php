<?php
class Perfil {
    private $id;
    private $email;
    private $contrasena;
    private $nombre;
    private $apellido;
    private $telefono;

    // Constructor
    public function __construct($id = null, $email = null, $contrasena = null, 
                                $nombre = null, $apellido = null, $telefono = null) {
        $this->id = $id;
        $this->email = $email;
        $this->contrasena = $contrasena;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->telefono = $telefono;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function getContrasena() { return $this->contrasena; }
    public function getNombre() { return $this->nombre; }
    public function getApellido() { return $this->apellido; }
    public function getTelefono() { return $this->telefono; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setEmail($email) { $this->email = $email; }
    public function setContrasena($contrasena) { $this->contrasena = $contrasena; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setApellido($apellido) { $this->apellido = $apellido; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
}
?>