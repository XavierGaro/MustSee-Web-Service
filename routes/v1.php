<?php
/**
 * Script per afegir les rutes de la versió 1 de la API RESTful MustSee.
 *
 * @author Xavier García
 */
use MustSee\Database\DataBaseManager;
use MustSee\Router\Route;
use MustSee\Router\RouteManager;

// Obtenim el nom d'aquest script, que es farà servir per determinar el patró de aquestes rutes
$version = basename(__FILE__, '.php');

// Primer creem els Callable que farem servir com a Middleware i Funcions per les rutes.

/**
 * Aquest Middleware comprova que el correu i password passats com variables POST coincideixin a la
 * base de dades, si no es així es mostrarà un error.
 *
 * @param DataBaseManager $dbm          connexió al gestor de bases de dades
 * @param RouteManager    $routeManager RouteManager al que es processarà aquesta ruta
 * @return Callable que comprova si les dades de connexió son correctes i si no mostra un missatge
 *                                      d'error
 */
$authenticate = function (DataBaseManager $dbm, RouteManager $routeManager) {
    return function () use ($dbm, $routeManager) {
        if (isset($_POST['correu'], $_POST['password']) === true) {
            $correu   = htmlspecialchars($_POST['correu'], ENT_QUOTES);
            $password = htmlspecialchars($_POST['password'], ENT_QUOTES);
            if ($dbm->comprovarContrasenya($correu, $password) === false) {
                $routeManager->renderError("Error al autenticar", 500);
            }
        } else {
            $routeManager->renderError("No s'han passat tots els paràmetres", 500);
        }
    };
};

/**
 * Aquesta funció serà cridada per afegir un comentari a la base de dades.
 *
 * @param DataBaseManager $dbm          connexió al gestor de bases de dades
 * @param RouteManager    $routeManager RouteManager al que es processarà aquesta ruta
 * @return Callable que afegeix un comentari a la base de dades o mostra un error si no es pot
 *                                      afegir
 */
$postComment = function (DataBaseManager $dbm, RouteManager $routeManager) {
    return function ($id) use ($dbm, $routeManager) {
        try {
            // Sanegem les dades abans de fer la consulta
            $comentari = htmlspecialchars($_POST['comentari'], ENT_QUOTES);
            $correu    = htmlspecialchars($_POST['correu'], ENT_QUOTES);
            $dbm->addComentariToLloc($id, $correu, $comentari);

            return ['message' => "S'ha afegit el comentari correctament"];
        } catch (Exception $ex) {
            // Si no s'ha establert el comentari o hi ha algun error al afegir-lo
            $routeManager->renderError("Error al afegir el comentari", 500);
        }
    };
};

// Afegim les rutes
$routes[] = new Route(
        "GET",
        "/$version/:llocs",
        array($dbm, 'getLlocs'),
        array('llocs' => 'llocs'. RouteManager::EXTENSION),
        'llocs'
);

$routes[] = new Route(
        "GET",
        "/$version/llocs/:id",
        array($dbm, 'getLloc'),
        array('id' => RouteManager::RESOURCE_INT),
        'lloc'
);

$routes[] = new Route(
        "GET",
        "/$version/:categories",
        array($dbm, 'getCategories'),
        array('categories' => 'categories' . RouteManager::EXTENSION),
        'categories'
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

$routes[] = new Route(
        "GET",
        "/$version/usuaris/:id",
        array($dbm, 'getUsuariById'),
        array('id' => RouteManager::RESOURCE_INT),
        'usuari'
);


$routes[] = new Route(
        "POST",
        "/$version/comentaris/llocs/:id",
        $postComment($dbm, $routeManager),
        array('id' => RouteManager::RESOURCE_INT),
        'comentari',
        $authenticate($dbm, $routeManager)
);