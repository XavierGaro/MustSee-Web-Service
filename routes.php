<?php

use MustSee\Database\DataBaseManager;
use MustSee\Router\Router;

require_once 'Slim/Slim.php';
require_once 'MustSee/Database/DatabaseManager.php';
require_once 'MustSee/Router/Router.php';
require_once 'Serializer/SerializerFactory.php';
\Slim\Slim::registerAutoloader();

define('ENCODING', 'utf-8');

$app = new \Slim\Slim();
$dbm = DataBaseManager::getInstance('MySQL');

$app->view->setData(array('encoding' => ENCODING));

// Formats acceptats per aquesta aplicació
$formats = array(
        'xml'  => 'application/xml',
        'json' => 'application/json'
);



$router= new Router($dbm, $app);
$router->setFormats($formats);
$router->run();


/*// Obtenim el format
$format = getFormat($app, $formats);

// Preparem la resposta
setResponse($app, $format, $formats);


function getFormat($app, array $formats) {
    // Comprovem si s'ha especificat una extensió a la ruta
    $path = $app->request->getPath();

    // Comprovem si hi ha un punt
    $dotPos = strpos($path, '.');
    $format = substr($path, $dotPos + 1);

    // Comprovem que no hi hagi cap barra, i es trobi al array de formats.
    if ($format !== false && strstr($format, '/') == false && array_key_exists($format, $formats)) {
        return $format;
    }

    // Si no s'ha trobat cap format vàlid comprovem el tipus de fitxer acceptat
    $accept = $app->request->headers->get('Accept');
    foreach ($formats as $format => $contentType) {
        if (stripos($accept, $contentType) !== false) {
            return $format;
        }
    }

    // Si no s'ha trobat cap tipus ni com a extensió ni com acceptat llencem una excepció
    throw new Exception ("No es reconeix cap tipus de resposta");
}


function setResponse($app, $format, $formats) {
    $app->response()->header('Content-Type', $formats[$format] . ';charset=' . ENCODING);
    $app->template   = 'template' . strtoupper($format) . ".php";
    $app->serializer = \Serializer\SerializerFactory::getInstance($format);
}


$app->group('/v1', function () use ($app, $dbm) {
    require 'routes/v1.php';
});

$app->group('/v2', function () {
    require 'routes/v2.php';
});



$app->run();
*/