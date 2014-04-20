<?php
namespace MustSee\Data;

/**
 * Class Categoria
 * Classe de dades immutable que emmagatzema una categoria.
 *
 * @author  Xavier García
 * @package MustSee\Data
 */
class Categoria {
    private $id;
    private $descripcio;

    /**
     * Crea una categoria la id es opcional per crear objectes a la memòria.
     *
     * @param string $descripcio nom de la categoria
     * @param int    $id         identificador de la categoria
     */
    public function __construct($descripcio, $id = -1) {
        $this->descripcio = $descripcio;
        $this->id         = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getDescripcio() {
        return $this->descripcio;
    }
}