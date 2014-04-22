<?php
namespace MustSee\Database;

use MustSee\Data\Categoria;
use MustSee\Data\Comentari;
use MustSee\Data\Imatge;
use MustSee\Data\Lloc;
use MustSee\Data\Usuari;

/**
 * Class DatabaseManager
 * Aquesta classe es un Singleton que gestiona els objectes de la aplicació MustSee emmagatzemats a
 * la base de dades.
 *
 * @todo    Gestionar els errors
 * @author  Xavier García
 * @package MustSee\Database
 */
class DatabaseManager {
    private static $instance;

    private $pdo;

    /**
     * S'han d'instanciar cridant a mètode DatabaseManager::getInstance().
     *
     * @param \PDO $pdo instància de la classe PDO inicialitzada.
     */
    private function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Aquest mètode retorna una instància d'aquest gestor correctament inicialitzat. El array
     * de configuració només es fa servir la primera vegada que es genera la instància,
     * després s'ignora.
     *
     * @param mixed[] $config array amb les dades de configuració,
     *                        els valors necessaris son:
     *                        'db_driver' => ha de ser un valor acceptat per PDOFactory
     *                        'db_host' => host on es troba la base de dades
     *                        'db_name' => nom de la base de dades
     *                        'db_user' => nom d'usuari amb accés
     *                        'db_pass' => password del usuari
     * @return DatabaseManager la instància del gestor de dades
     */
    static function getInstance($config) {
        if (self::$instance === null) {
            $pdo            = DatabaseFactory::getConnection($config);
            self::$instance = new DatabaseManager($pdo);
        }

        return self::$instance;
    }

    /**
     * Retorna la llista completa de categories de la base de dades.
     *
     * @return Categoria[] Array amb totes les categories
     */
    public function getCategories() {
        $statement = $this->pdo->prepare('SELECT id_categoria, descripcio FROM categories');
        $statement->execute();
        $result = $statement->fetchAll();

        $categories = array();
        foreach ($result as $row) {
            array_push($categories, new Categoria(
                    $row['descripcio'],
                    $row['id_categoria']
            ));
        }

        return $categories;
    }

    /**
     * Retorna la llista completa de llocs de la base de dades, incloent les imatges i els
     * comentaris.
     *
     * @return Lloc[] Array amb tots els llocs
     */
    public function getLlocs() {
        $statement = $this->pdo->prepare('SELECT id_llocs, nom, descripcioExtesa, categories_id_categoria, latitud, longitud FROM llocs');
        $statement->execute();
        $result = $statement->fetchAll();

        $llocs = array();
        foreach ($result as $row) {
            $lloc = new Lloc(
                    $row['nom'],
                    $row['descripcioExtesa'],
                    $row['categories_id_categoria'],
                    $row['latitud'],
                    $row['longitud'],
                    $row['id_llocs']);

            // Afegim les imatges i els comentaris
            $lloc->addImatges($this->getImatgesFromLloc($lloc->getId()));
            $lloc->addComentaris($this->getComentarisFromLloc($lloc->getId()));
            $llocs[] = $lloc;
        }

        return $llocs;
    }

    /**
     * Retorna el lloc demanat com argument, incloent les imatges i els comentaris.
     *
     * @param int $id id del lloc que volem obtenir
     * @return Lloc el lloc construït amb les dades de la base de dades o null si no es troba
     */
    public function getLloc($id) {
        $statement = $this->pdo->prepare('SELECT nom, descripcioExtesa, categories_id_categoria, latitud, longitud, id_llocs FROM llocs WHERE id_llocs = ?');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();

        if ($result = $statement->fetch()) {
            $lloc = new Lloc(
                    $result['nom'],
                    $result['descripcioExtesa'],
                    $result['categories_id_categoria'],
                    $result['latitud'],
                    $result['longitud'],
                    $result['id_llocs']);

            // Afegim les imatges i comentaris
            $lloc->addImatges($this->getImatgesFromLloc($id));
            $lloc->addComentaris($this->getComentarisFromLloc($lloc->getId()));

            return $lloc;
        } else {
            return null;
        }
    }

    /**
     * Retorna la llista de totes les imatges que pertanyen al lloc passat com argument.
     *
     * @param int $id lloc del que volem obtenir les imatges
     * @return Imatge[] array amb les totes les imatges del lloc
     */
    public function getImatgesFromLloc($id) {
        $statement = $this->pdo->prepare('SELECT id_foto, url, descripcio, llocs_id_llocs FROM fotos WHERE llocs_id_llocs = ?');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll();

        $imatges = array();
        foreach ($result as $row) {
            $imatges[] = new Imatge(
                    $row['descripcio'],
                    $row['url'],
                    $row['llocs_id_llocs'],
                    $row['id_foto']
            );
        }

        return $imatges;
    }

    /**
     * Retorna la imatge corresponen a la id passada com argument.
     *
     * @param int $id imatge a recuperar
     * @return Imatge la imatge corresponen o null si no hi ha cap
     */
    public function getImatge($id) {
        $statement = $this->pdo->prepare('SELECT id_foto, url, descripcio, llocs_id_llocs FROM fotos WHERE id_foto = ?');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();

        if ($result = $statement->fetch()) {
            return new Imatge(
                    $result['descripcio'],
                    $result['url'],
                    $result['llocs_id_llocs'],
                    $result['id_foto']
            );
        } else {
            return null;
        }
    }

    /**
     * Retorna tots els comentaris enllaçats al lloc passat com argument.
     *
     * @param  int $id lloc del que volem obtenir els comentaris
     * @return Comentari[] array amb tots els comentaris del lloc
     */
    public function getComentarisFromLloc($id) {
        $statement = $this->pdo->prepare('SELECT id_comentaris, text, perfil_users_id_usuari, llocs_id_llocs FROM comentaris WHERE llocs_id_llocs = ?');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll();

        $comentaris = array();
        foreach ($result as $row) {
            $comentaris[] = new Comentari(
                    $row['text'],
                    $row['perfil_users_id_usuari'],
                    $row['llocs_id_llocs'],
                    $row['id_comentaris']
            );
        }

        return $comentaris;
    }

    /**
     * Retorna tots els comentaris del usuari passat com argument.
     *
     * @param int $id id del usuari del que volem obtenir els comentaris
     * @return array Comentari amb els comentaris del lloc
     */
    public function getComentarisFromUsuari($id) {
        $statement = $this->pdo->prepare('SELECT id_comentaris, text, llocs_id_llocs, perfil_users_id_usuari FROM comentaris WHERE perfil_users_id_usuari = ?');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll();

        $comentaris = array();
        foreach ($result as $row) {
            $comentaris[] = new Comentari(
                    $row['text'],
                    $row['perfil_users_id_usuari'],
                    $row['llocs_id_llocs'],
                    $row['id_comentaris']
            );
        }

        return $comentaris;
    }

    /**
     * Recupera les dades del usuari amb la id passada com argument.
     *
     * @param int $id id del usuari
     * @return Usuari dades del usuari
     */
    public function getUsuariById($id) {
        $statement = $this->pdo->prepare('SELECT correu, nom, cognom, id_usuari FROM users INNER JOIN perfil ON (users_id_usuari = id_usuari) WHERE id_usuari = ?');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();

        if ($result = $statement->fetch()) {
            $usuari = new Usuari(
                    $result['nom'] . ' ' . $result['cognom'],
                    $result['correu'],
                    $result['id_usuari']
            );

            return $usuari;
        } else {
            return null;
        }
    }

    /**
     * Recupera les dades del usuari corresponent al correu passat com argument.
     *
     * @param string $correu correu del usuari
     * @return Usuari dades del usuari
     */
    public function getUsuariByCorreu($correu) {
        $statement = $this->pdo->prepare('SELECT correu, nom, cognom, id_usuari FROM users INNER JOIN perfil ON (users_id_usuari = id_usuari) WHERE correu = ?');
        $statement->bindValue(1, $correu, \PDO::PARAM_STR);
        $statement->execute();

        if ($result = $statement->fetch()) {
            $usuari = new Usuari(
                    $result['nom'] . ' ' . $result['cognom'],
                    $result['correu'],
                    $result['id_usuari']
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
     * @return bool true si s'ha trobar o fals en cas contrari
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

    /**
     * Afegeix un comentari a la base de dades lligat al lloc i el usuari passats com argument.
     *
     * @param int    $idLloc    id del lloc
     * @param string $correu    correu del usuari
     * @param string $comentari text del comentari
     * @throws \Exception si el Lloc no existeix o no hi ha un error al inserir el comentari
     */
    public function addComentariToLloc($idLloc, $correu, $comentari) {
        // Comprovem si existeix el lloc
        if ($this->getLloc($idLloc) === null) {
            throw new \Exception('El lloc no existeix');
        }

        // Obtenim la data d'avui
        $today = date("y.m.d");

        // Obtenim la id del usuari
        $idUsuari = $this->getUsuariByCorreu($correu)->getId();

        $statement = $this->pdo->prepare('INSERT INTO comentaris(text, data_publi,
        llocs_id_llocs, perfil_users_id_usuari) VALUES (?, ?, ?, ?)');
        $statement->bindValue(1, $comentari, \PDO::PARAM_STR);
        $statement->bindValue(2, $today, \PDO::PARAM_STR);
        $statement->bindValue(3, $idLloc, \PDO::PARAM_INT);
        $statement->bindValue(4, $idUsuari, \PDO::PARAM_INT);

        if ($statement->execute() === false) {
            throw new \Exception('Error al inserir el comentari');
        }
    }
}