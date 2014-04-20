<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

use MustSee\Database\DatabaseManager;
use MustSee\Router\RouteManager;

require_once 'Slim/Slim.php';
require_once 'MustSee/Database/DatabaseManager.php';
require_once 'MustSee/Router/RouteManager.php';
\Slim\Slim::registerAutoloader();

// Inicialitzem els objectes i el arxiu de configuració
$app          = new \Slim\Slim();
$config       = parse_ini_file('db_config.ini');
$routeManager = new RouteManager($app);
$dbm          = DatabaseManager::getInstance($config);

// Carreguem els scripts amb les rutes
require_once 'routes/v1.php';

// Afegim totes les routes creades al script al RouteManager
$routeManager->addRoutes($routes);

// Iniciem el processament de la ruta
$routeManager->run();





