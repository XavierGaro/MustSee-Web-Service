<?php
namespace MustSee\Data;

class Usuari{
    private $id;
    private $nom;
    private $correu;

    function __construct()
    {
        $this->nom = func_get_arg(0);
        $this->correu = func_get_arg(1);

        if (func_num_args()===3) {
            $this->id = func_get_arg(2);
        }

    }

    public function getCorreu()
    {
        return $this->correu;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

}