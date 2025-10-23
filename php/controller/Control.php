<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Perfil.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Admin.php';

class Control {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }





    
}
?>