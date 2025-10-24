<?php
class Usuario extends Perfil
{
    private $genero;

    public function __construct($id, $genero, $nombre, $email, $contrasena)
    {
        parent::__construct($id, $email, $contrasena, $nombre);
        $this->genero = $genero;
    }


    public function getGenero()
    {
        return $this->genero;
    }

    public function setGenero($genero)
    {
        $this->genero = $genero;
    }
}
