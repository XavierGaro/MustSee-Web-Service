<?php
require_once 'Slim/Slim.php';
require_once 'MustSee/Database/DatabaseManager.php';
require_once 'Serializer/SerializerFactory.php';
\Slim\Slim::registerAutoloader();

//define('ENCODING', 'iso-8859-1');
define('ENCODING', 'UTF-8');

$app = new \Slim\Slim();

$dbm        = MustSee\Database\DataBaseManager::getInstance('MySQL');
$serializer = \Serializer\SerializerFactory::getInstance('xml');


$app->get('/', function () use ($dbm, $serializer) {
    echo "Root!"; // TODO: Redirigir a la pagina principal del lloc
});


// Routes per obtenir la informació via XML
$app->group('/xml', function () use ($app, $serializer, $dbm) {
    $app->response->headers->set('Content-Type', 'application/xml');

    // Informació dels llocs
    $app->group('/llocs', function () use ($app, $serializer, $dbm) {

        // El lloc corresponen a la id
        $app->get('/:id', function ($id) use ($app, $serializer, $dbm) {
            $xml = $dbm->getLloc($id);
            $app->view->setData(array(
                            'data' => $serializer->getSerialized($xml, 'llocs'),
                            'encoding' => ENCODING)
            );
            $app->render('XMLTemplate.php');
            $app->response->setStatus(200);
        });

        // Tots els llocs
        $app->get('/', function () use ($app, $serializer, $dbm) {
            $xml = $dbm->getLlocs();
            $app->view->setData(array(
                            'data' => $serializer->getSerialized($xml, 'llocs'),
                            'encoding' => ENCODING)
            );
            $app->render('XMLTemplate.php');
            $app->response->setStatus(200);
        });
    });

    // Informació de les categories
    $app->get('/categories', function () use ($app, $serializer, $dbm) {
        $xml = $dbm->getCategories();
        $app->view->setData(array(
                        'data' => $serializer->getSerialized($xml, 'categories'),
                        'encoding' => ENCODING)
        );
        $app->render('XMLTemplate.php');
        $app->response->setStatus(200);
    });

});
// para pasar a los Templates
// 'encoding' = utf-8
// 'data' = data per mostrar


$app->run();
