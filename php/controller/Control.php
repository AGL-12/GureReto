<?php
require_once  '/../config/Database.php';
require_once  '/../models/Perfil.php';
require_once  '/../models/Usuario.php';
require_once  '/../models/Admin.php';

class Control {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }





    
}
?>