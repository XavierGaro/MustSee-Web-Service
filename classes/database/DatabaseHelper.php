<?php

class DBHelper {

    public static function getConnection($driver, $variables){

        switch ($driver) {
            case 'MySQL':
                return DBHelper::getInstanceMySQL($variables);

            case 'PostgreSQL':
                return DBHelper::getInstancePostgreSQL($variables);

            default:
                // Sense implementar
        }
    }

    private static function getInstanceMySQL($variables)
    {
        try {
            $driver = "mysql:host={$variables['db_host']};dbname={$variables['db_name']};charset=utf8mb4";
            $pdo = new PDO($driver, $variables['db_user'], $variables['db_pass'],
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => false
                )
            );
        } catch (PDOException $ex) {
            print($ex->getMessage());
        }
        return $pdo;
    }

    // TODO: Sense provar
    private static function getInstancePostgreSQL($variables)
    {
        try {
            $driver = "pgsql:dbname={$variables['db_name']};host={$variables['db_host']};user={$variables['db_user']};password={$variables['db_pass']}";
            $pdo = new PDO($driver);
        } catch (PDOException $ex) {
            print($ex->getMessage());
        }
        return $pdo;
    }

} 