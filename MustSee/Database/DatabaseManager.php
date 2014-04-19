<?php
namespace MustSee\Database;

use MustSee\Data\Categoria;
use MustSee\Data\Comentari;
use MustSee\Data\Imatge;
use MustSee\Data\Lloc;
use MustSee\Data\Usuari;

/**
 * Class DataBaseManager
 * Classe per gestionar els objectes de la aplicació MustSee emmagatzemats a la base de dades.
 *
 * @author Xavier García
 */
class DataBaseManager {

    private $pdo;

    /**
     * El constructor es privat, s'han d'instanciar cridant a mètode DataBaseManager#getInstance
     * ().
     *
     * @param \PDO $pdo instància de la classe PDO inicialitzada.
     */
    private function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Aquest mètode retorna una instància d'aquest gestor correctament inicialitzat.
     *
     * @param array $config dades de configuració
     * @internal param string $dbConfig fitxer de configuració amb les dades per connectar
     * @return DataBaseManager instància del gestor de dades
     */
    static function getInstance($config) {
        $pdo = DatabaseFactory::getConnection($config);

        return new DataBaseManager($pdo);
    }

    /**
     * Retorna la llista completa de categories de la base de dades.
     *
     * @return array Categoria amb totes les categories.
     */
    public function getCategories() {
        $statement = $this->pdo->prepare('SELECT id_categoria, descripcio FROM categories');
        $statement->execute();
        $result = $statement->fetchAll();

        $categories = array();
        foreach ($result as $categoria) {
            array_push($categories, new Categoria(
                    $categoria['descripcio'],
                    $categoria['id_categoria']
            ));
        }

        return $categories;
    }

    /**
     * Retorna la llista completa de llocs de la base de dades.
     *
     * @return array Lloc amb tots els llocs.
     */
    public function getLlocs() {
        $statement = $this->pdo->prepare('SELECT id_llocs, nom, descripcioExtesa, categories_id_categoria, latitud, longitud FROM llocs');
        $statement->execute();
        $result = $statement->fetchAll();

        $llocs = array();
        foreach ($result as $lloc) {
            array_push($llocs, new Lloc(
                    $lloc['nom'],
                    $lloc['descripcioExtesa'],
                    $lloc['categories_id_categoria'],
                    $lloc['latitud'],
                    $lloc['longitud'],
                    $lloc['id_llocs']
            ));
        }

        return $llocs;
    }

    /**
     * Retorna el lloc demanat com argument.
     *
     * @param integer $id id del lloc que volem obtenir.
     * @return \MustSee\Data\Lloc lloc construït amb les dades de la base de dades.
     */
    public function getLloc($id) {
        $statement = $this->pdo->prepare('SELECT nom, descripcioExtesa, categories_id_categoria, latitud, longitud FROM llocs WHERE id_llocs = ?');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();

        if ($result = $statement->fetch()) {
            $lloc = new Lloc(
                    $result['nom'],
                    $result['descripcioExtesa'],
                    $result['categories_id_categoria'],
                    $result['latitud'],
                    $result['longitud'],
                    $id);

            // Afegim les imatges
            $lloc->addImatges($this->getImatgesFromLloc($id));

            return $lloc;
        } else {
            return null;
        }


    }

    /**
     * Retorna la llista de totes les imatges que pertanyen al lloc passat com argument.
     *
     * @param integer $llocId lloc del que volem obtenir les imatges.
     * @return array Imatge amb les imatges.
     */
    public function getImatgesFromLloc($llocId) {
        $statement = $this->pdo->prepare('SELECT id_foto, url, descripcio FROM fotos WHERE llocs_id_llocs = ?');
        $statement->bindValue(1, $llocId, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll();

        $imatges = array();
        foreach ($result as $imatge) {
            array_push($imatges, new Imatge(
                    $imatge['descripcio'],
                    $imatge['url'],
                    $llocId,
                    $imatge['id_foto']
            ));
        }

        return $imatges;
    }

    /**
     * Retorna la imatge corresponen a la id passada com argument.
     *
     * @param integer $id imatge a recuperar.
     * @return Imatge la imatge corresponen.
     */
    public function getImatge($id) {
        $statement = $this->pdo->prepare('SELECT id_foto, url, descripcio, llocs_id_llocs FROM fotos WHERE id_foto = ?');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();

        $imatge = new Imatge(
                $result['descripcio'],
                $result['url'],
                $result['llocs_id_llocs'],
                $id
        );

        return $imatge;
    }

    /**
     * Retorna tots els comentaris enllaçats al lloc passat com argument.
     *
     * @param  integer $llocId lloc del que volem obtenir els comentaris.
     * @return array Comentari amb els comentaris del lloc.
     */
    public function getComentarisFromLloc($llocId) {
        $statement = $this->pdo->prepare('SELECT id_comentaris, text, perfil_users_id_usuari FROM comentaris WHERE llocs_id_llocs = ?');
        $statement->bindValue(1, $llocId, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll();

        $comentaris = array();
        foreach ($result as $row) {
            array_push($comentaris, new Comentari(
                    $row['text'],
                    $row['perfil_users_id_usuari'],
                    $llocId,
                    $row['id_comentaris']
            ));
        }

        return $comentaris;
    }

    /**
     * Retorna tots els comentaris del usuari passat com argument.
     *
     * @param integer $usuariId id del usuari del que volem obtenir els comentaris.
     * @return array Comentari amb els comentaris del lloc.
     */
    public function getComentarisFromUsuari($usuariId) {
        $statement = $this->pdo->prepare('SELECT id_comentaris, text, llocs_id_llocs FROM comentaris WHERE perfil_users_id_usuari = ?');
        $statement->bindValue(1, $usuariId, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll();

        $comentaris = array();
        foreach ($result as $row) {
            array_push($comentaris, new Comentari(
                    $row['text'],
                    $usuariId,
                    $row['llocs_id_llocs'],
                    $row['id_comentaris']
            ));
        }

        return $comentaris;
    }

    /**
     * Recupera les dades del usuari passat com argument.
     *
     * @param integer $id id del usuari.
     * @return Usuari dades del usuari.
     */
    public function getUsuari($id) {
        $statement = $this->pdo->prepare('SELECT correu, nom, cognom FROM users INNER JOIN perfil ON (users_id_usuari = id_usuari) WHERE id_usuari = ?');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();

        if ($result = $statement->fetch()) {
            $usuari = new Usuari(
                    $result['nom'] . ' ' . $result['cognom'],
                    $result['correu'],
                    $id
            );

            return $usuari;
        } else {
            return null;
        }

    }

    /**
     * Comprova que el correu i la contrasenya son correctes.
     *
     * @param string $correu   correu per comprovar
     * @param string $password contrasenya per comprovar
     * @return bool cert si s'ha trobar o fals en cas contrari
     */
    public function comprovarContrasenya($correu, $password) {
        // Convertim el password en hash
        $hash      = hash('sha1', $password);
        $statement = $this->pdo->prepare('SELECT correu, password FROM users WHERE correu = ? AND password = ? ');
        $statement->bindValue(1, $correu, \PDO::PARAM_STR);
        $statement->bindValue(2, $hash, \PDO::PARAM_STR);
        $statement->execute();

        // Comprovem si hi ha cap coincidència
        if ($statement->fetch()) {
            return true;
        } else {
            return false;
        }
    }
}
