<?php
namespace MustSee\Data;

/**
 * Class Usuari
 * Classe de dades immutable que emmagatzema un usuari. Entre les informacions que s'emmagatzema
 * no es troba el password, la comprovació sempre es realitzarà enviant la combinació a la base
 * de dades i obtenint un booleà cert o fals.
 *
 * @author  Xavier García
 * @package MustSee\Data
 */
class Usuari {
    private $id;
    private $nom;
    private $correu;

    /**
     * Crea un usuari amb les seves dades
     *
     * @param string $nom    nom del usuari
     * @param string $correu correu del usuari
     * @param int    $id     id del usuari
     */
    public function __construct($nom, $correu, $id = -1) {
        $this->nom    = $nom;
        $this->correu = $correu;
        $this->id     = $id;
    }

    public function getCorreu() {
        return $this->correu;
    }

    public function getId() {
        return $this->id;
    }

    public function getNom() {
        return $this->nom;
    }
}