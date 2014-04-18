<?php
namespace MustSee\Data;

class Categoria
{
    private $id = -1;
    private $descripcio;

    function __construct()
    {
        $this->descripcio = func_get_arg(0);

        if (func_num_args()===2) {
            $this->id = func_get_arg(1);
        }
    }

    function getId()
    {
        return $this->id;
    }

    function getDescripcio()
    {
        return $this->descripcio;
    }
}