<?php

class Lloc
{
    // Propietats immutables
    private $id;
    private $nom;
    private $descripcio;
    private $categoriaId;
    private $latitud;
    private $longitud;

    // Propietats mutables
    private $imatges;

    function __construct()
    {
        $this->nom = func_get_arg(0);
        $this->descripcio = func_get_arg(1);
        $this->categoriaId = func_get_arg(2);
        $this->latitud = func_get_arg(3);
        $this->longitud = func_get_arg(4);

        if (func_num_args()===6) {
            $this->id = func_get_arg(5);
        }

    }

    public function getCategoriaId()
    {
        return $this->categoriaId;
    }

    public function getDescripcio()
    {
        return $this->descripcio;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLatitud()
    {
        return $this->latitud;
    }

    public function getLongitud()
    {
        return $this->longitud;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getImatges() {
        return $this->imatges;
    }

    public function addImatges(array $imatges) {
        if (is_array($this->imatges)) {
            array_push($this->imatges, $imatges);
        } else {
            $this->imatges = $imatges;
        }
    }

}