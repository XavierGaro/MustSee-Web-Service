<?php
namespace MustSee\Database;
/**
 * Class DatabaseFactory
 * Aquesta classe es una factoria per crear instàncies de la classe PDO per diferents SGBD.
 *
 * @author  Xavier García
 * @package MustSee\Database
 */
class DatabaseFactory {

    /**
     * Aquesta classe no es pot instanciar.
     */
    private function __construct() {
    }

    /**
     * Retorna un objecte PDO inicialitzat amb les dades passades com argument.
     *
     * @param mixed[] $config array amb les dades de configuració,
     *                        els valors necessaris son:
     *                        'db_driver' => ha de ser un valor acceptat per PDOFactory
     *                        'db_host' => host on es troba la base de dades
     *                        'db_name' => nom de la base de dades
     *                        'db_user' => nom d'usuari amb accés
     *                        'db_pass' => password del usuari
     * @throws \Exception si no el tipus de base de dades no es vàlid
     * @return \PDO correctament inicialitzat
     */
    public static function getConnection($config) {
        switch (strtolower($config['db_driver'])) {
            case 'mysql':
                return DatabaseFactory::getInstanceMySQL($config);

            case 'postgresql':
                return DatabaseFactory::getInstancePostgreSQL($config);

            default:
                throw new \Exception('El tipus de base de dades no es vàlid');
        }
    }

    /**
     * Crea una instància de PDO amb les dades passades com argument per a una base de dades MySQL.
     *
     * @param mixed[] $config array associatiu amb les variables per crear la connexió.
     * @throws \Exception si hi ha algun error al crear la connexió
     * @return \PDO correctament inicialitzat
     */
    private static function getInstanceMySQL($config) {
        try {
            $driver = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
            $pdo    = new \PDO($driver, $config['db_user'], $config['db_pass'],
                    array(
                            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_PERSISTENT => false
                    )
            );
        } catch (\PDOException $ex) {
            throw new \Exception("Error al crear la connexió: {$ex->getMessage()}");
        }

        return $pdo;
    }

    /**
     * Crea una instància de PDO amb les dades passades com argument per a una base de dades MySQL.
     *
     * @todo no s'ha provat
     * @param mixed[] $config array associatiu amb les variables per crear la connexió
     * @throws \Exception si hi ha algun error al crear la connexió
     * @return \PDO correctament inicialitzat
     */
    private static function getInstancePostgreSQL($config) {
        try {
            $driver = "pgsql:dbname={$config['db_name']};host={$config['db_host']};user={$config['db_user']};password={$config['db_pass']}";
            $pdo    = new \PDO($driver);
        } catch (\PDOException $ex) {
            throw new \Exception("Error al crear la connexió: {$ex->getMessage()}");
        }

        return $pdo;
    }
} 