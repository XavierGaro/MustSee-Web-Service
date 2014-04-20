<?php
namespace MustSee\Data;

/**
 * Class Comentari
 * Classe de dades immutable que emmagatzema un comentari.
 *
 * @author  Xavier García
 * @package MustSee\Data
 */
class Comentari {
    private $id;
    private $text;
    private $usuariId;
    private $llocId;

    /**
     * Crea un comentari enllaçat amb un lloc i un usuari per las seves ids.
     *
     * @param string $text     text del comentari
     * @param int    $usuariId id del usuari
     * @param int    $llocId   io del lloc
     * @param int    $id       id del comentari
     */
    public function __construct($text, $usuariId, $llocId, $id = -1) {
        $this->text     = $text;
        $this->usuariId = $usuariId;
        $this->llocId   = $llocId;
        $this->id       = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getLlocId() {
        return $this->llocId;
    }

    public function getUsuariId() {
        return $this->usuariId;
    }

    public function getText() {
        return $this->text;
    }
}
