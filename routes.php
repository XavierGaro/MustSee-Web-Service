<?php
require_once 'Slim/Slim.php';
require_once 'MustSee/Database/DatabaseManager.php';
require_once 'Serializer/SerializerFactory.php';
\Slim\Slim::registerAutoloader();

define('ENCODING', 'utf-8');

$app = new \Slim\Slim();
$dbm = MustSee\Database\DataBaseManager::getInstance('MySQL');

$app->view->setData(array('encoding' => ENCODING));

$app->get('/xml/:query+', function () use ($app) {
    // Es un fitxer XML
    $app->response()->header('Content-Type', 'application/xml;charset=' . ENCODING);
    $app->template   = "XMLTemplate.php";
    $app->serializer = \Serializer\SerializerFactory::getInstance('xml');
    $app->pass();
});

$app->get('/json/:query+', function () use ($app) {
    // Es un fitxer JSON
    $app->response()->header('Content-Type', 'application/json;charset=' . ENCODING);
    $app->template   = "JSONTemplate.php";
    $app->serializer = \Serializer\SerializerFactory::getInstance('json');
    $app->pass();
});


$app->group('/xml', function () use ($app, $dbm) {
    require 'routes/common.php';
});

$app->group('/json', function () use ($app, $dbm) {
    require 'routes/common.php';
});

$app->run();
