<?php
use MustSee\Router\Route;
use MustSee\Router\RouteManager;

define ('VERSION', 'v1');

$routes[] = new Route(
        '/v1/:llocs',
        array($dbm, 'getLlocs'),
        array('llocs' => 'llocs\.\w+'),
        'llocs',
        array($router, 'noParams')
);

$routes[] = new Route(
        '/v1/llocs/:id',
        array($dbm, 'getLloc'),
        array('id' => RouteManager::RESOURCE_INT),
        'llocs'
);

$routes[] = new Route(
        '/v1/:categories',
        array($dbm, 'getCategories'),
        array('categories' => 'categories.\w+'),
        'categories',
        array($router, 'noParams')
);

$routes[] = new Route(
        '/v1/comentaris/usuari/:id',
        array($dbm, 'getComentarisFromUsuari'),
        array('id' => RouteManager::RESOURCE_INT),
        'comentaris'
);

$routes[] = new Route(
        '/v1/comentaris/llocs/:id',
        array($dbm, 'getComentarisFromLloc'),
        array('id' => RouteManager::RESOURCE_INT),
        'comentaris'
);

// Exemple amb dos middleware
$routes[] = new Route(
        '/v1/:llacs',
        array($dbm, 'getLlocs'),
        array('llocs' => 'llocs.\w+'),
        'llocs',
        array(
                array($router, 'noParams'),
                array($router, 'noParams')
        )
);


$router->addRoutes($routes);