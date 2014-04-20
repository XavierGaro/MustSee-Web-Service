<?php
use MustSee\Database\DataBaseManager;
use MustSee\Router\Route;
use MustSee\Router\RouteManager;

$version = basename(__FILE__, '.php');

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
        "/$version/comentaris/usuaris/:id",
        array($dbm, 'getComentarisFromUsuari'),
        array('id' => RouteManager::RESOURCE_INT),
        'comentaris'
);

$routes[] = new Route(
        "GET",
        "/$version/comentaris/llocs/:id",
        array($dbm, 'getComentarisFromLloc'),
        array('id' => RouteManager::RESOURCE_INT),
        'comentaris'
);


// middleware
$authenticateForRole = function (DataBaseManager $dbm, RouteManager $routeManager) {
    return function () use ($dbm, $routeManager) {
        $correu   = htmlspecialchars($_POST['correu'], ENT_QUOTES);
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES);
        if ($dbm->comprovarContrasenya($correu, $password) === false) {
            $routeManager->renderError("Error al autenticar", 500);
        }
    };
};

//middleware
$postComment = function (DataBaseManager $dbm, RouteManager $routeManager) {
    return function ($id) use ($dbm, $routeManager) {
        $comentari = htmlspecialchars($_POST['comentari'], ENT_QUOTES);
        $id_usuari    = htmlspecialchars($_POST['id'], ENT_QUOTES);

        try {
            $dbm->addComentariToLloc($id, $id_usuari, $comentari);
            return ['message' => "S'ha afegit el comentari correctament"];

        } catch (Exception $ex) {
            $routeManager->renderError("Error al afegir el comentari", 500);
        }
    };
};

$routes[] = new Route(
        "POST",
        "/$version/comentaris/llocs/:id",
        $postComment($dbm, $routeManager),
        array('id' => RouteManager::RESOURCE_INT),
        'comentari',
        $authenticateForRole($dbm, $routeManager)
);


$routeManager->addRoutes($routes);

