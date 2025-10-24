<?php
require_once 'Perfil.php';

class Admin extends Perfil {
    private $cuenta_corriente;

    public function __construct($id = null, $email = null, $contrasena = null, $nombre = null, $apellido = null, $telefono = null, $cuenta_corriente = 0.0) {
        parent::__construct($id, $email, $contrasena, $nombre, $apellido, $telefono);
        $this->cuenta_corriente = $cuenta_corriente;
    }

    public function getCuentaCorriente() {
        return $this->cuenta_corriente;
    }

    public function setCuentaCorriente($cuenta_corriente) {
        $this->cuenta_corriente = $cuenta_corriente;
    }
}
?>
