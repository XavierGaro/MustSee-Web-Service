<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

use MustSee\Database\DataBaseManager;
use MustSee\Router\RouteManager;

require_once 'Slim/Slim.php';
require_once 'MustSee/Database/DatabaseManager.php';
require_once 'MustSee/Router/RouteManager.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$config = parse_ini_file('db_config.ini');

/** @var DataBaseManager $dbm */
$dbm = DataBaseManager::getInstance($config);

$routeManager = new RouteManager($app);

// Carregar rutes
// TODO Fer que carregui tots els fitxers de routes/ amb el format '$v[\d]+\.php^'
require_once 'routes/v1.php';


$routeManager->addRoutes($routes);


$routeManager->run();


