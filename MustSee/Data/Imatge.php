<?php
namespace MustSee\Data;

/**
 * Class Imatge
 * Classe de dades immutable que emmagatzema una imatge.
 *
 * @author  Xavier García
 * @package MustSee\Data
 */
class Imatge {
    private $id;
    private $titol;
    private $url;
    private $llocId;

    /**
     * Crea una imatge amb les dades passades com argument enllaçant-la a un lloc.
     *
     * @param string $titol  títol de la imatge
     * @param string $url    url on es troba la imatge
     * @param int    $llocId id del lloc
     * @param int    $id     id de la imatge
     */
    public function __construct($titol, $url, $llocId, $id = -1) {
        $this->titol  = $titol;
        $this->url    = $url;
        $this->llocId = $llocId;
        $this->id     = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getLlocId() {
        return $this->llocId;
    }

    public function getTitol() {
        return $this->titol;
    }

    public function getUrl() {
        return $this->url;
    }
}

