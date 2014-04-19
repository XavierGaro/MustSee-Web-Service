<?php

use MustSee\Database\DataBaseManager;
use MustSee\Router\Router;

require_once 'Slim/Slim.php';
require_once 'MustSee/Database/DatabaseManager.php';
require_once 'MustSee/Router/Router.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$dbm = DataBaseManager::getInstance('MySQL');

// Formats acceptats per aquesta aplicació
$formats = array(
        'xml'  => 'application/xml',
        'json' => 'application/json'
);

$router = new Router($dbm, $app);
$router->setFormats($formats);
$router->run();
