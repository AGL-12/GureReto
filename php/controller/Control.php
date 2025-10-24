<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');

// Rutas relativas desde este archivo
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/PerfilModel.php';
class Control
{
    private PerfilModel $perfilModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->perfilModel = new PerfilModel($db);
    }
    public function login($email, $password)
    {
        return $this->perfilModel->LoginPerfil($email, $password);
    }
}
