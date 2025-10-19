<?php
class Usuario {
    private $id;
    private $genero;

    public function __construct($id = null, $genero = null) {
        $this->id = $id;
        $this->genero = $genero;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getGenero() { return $this->genero; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setGenero($genero) { $this->genero = $genero; }
}
?>