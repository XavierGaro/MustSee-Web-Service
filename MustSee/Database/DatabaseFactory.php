<?php
namespace MustSee\Database;
/**
 * Class DatabaseFactory
 * Aquesta classe es una factoria per crear instàncies de la classe PDO per diferents SGBD.
 *
 * @author Xavier García
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
     * @param array $config array associatiu amb les variables per crear la connexió,
     *                      aquestes variables han de ser: db_driver, db_host, db_name,
     *                      db_user i db_pass
     * @throws \Exception si no el tipus de base de dades no es vàlid
     * @internal param string $driver nom del sistema gestor de base de dades
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
     * Crea una instància de PDO amb les dades passades com argument.
     *
     * @param array $variables array associatiu amb les variables per crear la connexió.
     * @return \PDO correctament inicialitzat
     */
    private static function getInstanceMySQL($variables) {
        try {
            $driver = "mysql:host={$variables['db_host']};dbname={$variables['db_name']};charset=utf8mb4";
            $pdo    = new \PDO($driver, $variables['db_user'], $variables['db_pass'],
                    array(
                            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_PERSISTENT => false
                    )
            );
        } catch (\PDOException $ex) {
            print($ex->getMessage());
        }

        return $pdo;
    }

    /**
     * Crea una instància de PDO amb les dades passades com argument.
     * TODO: sense provar.
     *
     * @param array $variables array associatiu amb les variables per crear la connexió.
     * @return \PDO correctament inicialitzat
     */
    private static function getInstancePostgreSQL($variables) {
        try {
            $driver = "pgsql:dbname={$variables['db_name']};host={$variables['db_host']};user={$variables['db_user']};password={$variables['db_pass']}";
            $pdo    = new \PDO($driver);
        } catch (\PDOException $ex) {
            print($ex->getMessage());
        }

        return $pdo;
    }
} 