<?php
namespace MustSee\Data;

/**
 * Class Lloc
 * Aquesta classe emmagatzema les dades lligades a un lloc. Totes les propietats son immutables
 * excepte el Lloc::$imatges i Lloc::$comentaris.
 *
 * Es proporcionen mètodes per afegir i obtenir tant les imatges com els comentaris,
 * però cap per modificar o eliminar els continguts.
 *
 * @author  Xavier García
 * @package MustSee\Data
 */
class Lloc {
    // Propietats immutables
    private $id;
    private $nom;
    private $descripcio;
    private $categoriaId;
    private $latitud;
    private $longitud;

    // Propietats mutables
    private $imatges;
    private $comentaris;

    /**
     * Crea un objecte de tipus lloc sense comentaris ni imatges.
     *
     * @param string $nom         nom del lloc
     * @param string $descripcio  descripció del lloc
     * @param int    $categoriaId ide de la categoria a la que pertany
     * @param float  $latitud     coordenada de latitud
     * @param float  $longitud    coordenada de longitud
     * @param int    $id          id del lloc
     */
    public function __construct($nom, $descripcio, $categoriaId, $latitud, $longitud, $id = -1) {
        $this->nom         = $nom;
        $this->descripcio  = $descripcio;
        $this->categoriaId = $categoriaId;
        $this->latitud     = $latitud;
        $this->longitud    = $longitud;
        $this->id          = $id;
    }

    public function getCategoriaId() {
        return $this->categoriaId;
    }

    public function getDescripcio() {
        return $this->descripcio;
    }

    public function getId() {
        return $this->id;
    }

    public function getLatitud() {
        return $this->latitud;
    }

    public function getLongitud() {
        return $this->longitud;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getImatges() {
        return $this->imatges;
    }

    public function getComentaris() {
        return $this->comentaris;
    }

    /**
     * Afegeix una imatge al lloc.
     *
     * @param Imatge $imatge imatge per afegir
     */
    public function addImatge(Imatge $imatge) {
        $this->imatges[] = $imatge;
    }

    /**
     * Afegeix un array d'imatges al lloc.
     *
     * @param Imatge[] $imatges array d'imatges per afegir
     */
    public function addImatges(array $imatges) {
        if (is_array($this->imatges)) {
            $this->imatges = array_merge($this->imatges, $imatges);
        } else {
            $this->imatges = $imatges;
        }
    }

    /**
     * Afegeix un comentari al lloc.
     *
     * @param Comentari $comentari comentari a afegir
     */
    public function addComentari(Comentari $comentari) {
        $this->comentaris[] = $comentari;
    }

    /**
     * Afegeix un array de comentaris al lloc.
     *
     * @param Comentari[] $comentaris array de comentaris a afegir
     */
    public function addComentaris(array $comentaris) {
        if (is_array($this->comentaris)) {
            $this->comentaris = array_merge($this->comentaris, $comentaris);
        } else {
            $this->comentaris = $comentaris;
        }
    }
}