<?php
use MustSee\Database\DataBaseManager;
use MustSee\Router\Route;
use MustSee\Router\RouteManager;

$version = basename(__FILE__, '.php');

// Si no existeix una instància de la base de dades generem una
if ($dbm === null) {
    $dbm = DataBaseManager::getInstance($config);
}

$routes[] = new Route(
        "GET",
        "/$version/:llocs",
        array($dbm, 'getLlocs'),
        array('llocs' => 'llocs(\.\w+)'),
        'llocs',
        array($routeManager, 'noParams')
);

$routes[] = new Route(
        "GET",
        "/$version/llocs/:id",
        array($dbm, 'getLloc'),
        array('id' => RouteManager::RESOURCE_INT),
        'llocs'
);

$routes[] = new Route(
        "GET",
        "/$version/:categories",
        array($dbm, 'getCategories'),
        array('categories' => 'categories(\.\w+)'),
        'categories',
        array($routeManager, 'noParams')
);

$routes[] = new Route(
        "GET",
        "/$version/comentaris/usuari/:id",
        array($dbm, 'getComentarisFromUsuari'),
        array('id' => RouteManager::RESOURCE_INT),
        'comentaris'
);

$routes[] = new Route(
        "GET",
        "/v1/comentaris/llocs/:id",
        array($dbm, 'getComentarisFromLloc'),
        array('id' => RouteManager::RESOURCE_INT),
        'comentaris'
);


// TODO: ruta per afegir comentaris
// Ha de passar per POST, i comprovar el correu i la contrasenya abans de afegir-lo


// middleware
$authenticateForRole = function (DataBaseManager $dbm, RouteManager $routeManager) {
    return function () use ($dbm, $routeManager) {
        $correu = $_POST['correu'];
        $password = $_POST['password'];
        //$correu   = 'xavierTest@hotmail.com'; // TODO: Extreure de variables POST i SANEJAR
        //$password = '123456---'; // TODO: Extreure de variables POST i SANEJAR

        if ($dbm->comprovarContrasenya($correu, $password) === false) {
            $routeManager->renderError("Error al autenticar", 500);
        }
    };
};

$mw = function() {
    echo "This is middleware!";
};

$routes[] = new Route(
        "POST",
        "/$version/:llocs",
        array($dbm, 'getCategories'),
        array('llocs' => 'llocs(\.\w+)'),
        'llocs',
        $authenticateForRole($dbm, $routeManager)
    // array($router, 'auth')
);


$routeManager->addRoutes($routes);

