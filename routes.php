<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

use MustSee\Database\DataBaseManager;
use MustSee\Router\RouteManager;

require_once 'Slim/Slim.php';
require_once 'MustSee/Database/DatabaseManager.php';
require_once 'MustSee/Router/RouteManager.php';
\Slim\Slim::registerAutoloader();

$app    = new \Slim\Slim();
$config = parse_ini_file('db_config.ini');
$dbm    = DataBaseManager::getInstance($config);

// Formats supportats pel serializar
// TODO: Moure a la factoria i que els agafi d'allà directament el RouteManager
$formats = array(
        'xml'  => 'application/xml',
        'json' => 'application/json'
);


$router = new RouteManager($dbm, $app);

// Carregar rutes
// TODO Fer que carregui tots els fitxers de routes/ amb el format '$v[\d]+\.php^'
require_once 'routes/v1.php';


$router->setFormats($formats);
$router->run();
