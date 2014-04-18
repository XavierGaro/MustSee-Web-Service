<?php
// Informació dels llocs
$app->group('/llocs', function () use ($app, $dbm) {

    // El lloc corresponen a la id
    $app->get('/:id', function ($id) use ($app, $dbm) {
        $xml = $dbm->getLloc($id);
        $app->view->setData(array(
                        'data'     => $app->serializer->getSerialized($xml, 'llocs'),
                        'encoding' => ENCODING)
        );
        $app->render($app->template);
        $app->response->setStatus(200);
    });

    // Tots els llocs
    $app->get('/', function () use ($app, $dbm) {
        $xml = $dbm->getLlocs();
        $app->view->setData(array(
                        'data'     => $app->serializer->getSerialized($xml, 'llocs'),
                        'encoding' => ENCODING)
        );
        $app->render($app->template);
        $app->response->setStatus(200);
    });
});

// Informació de les categories
$app->get('/categories/', function () use ($app, $dbm) {
    $xml = $dbm->getCategories();
    $app->view->setData(array(
                    'data'     => $app->serializer->getSerialized($xml, 'categories'),
                    'encoding' => ENCODING)
    );
    $app->render($app->template);
    $app->response->setStatus(200);
});

$app->group('/comentaris', function () use ($app, $dbm) {
    // Comentaris corresponents a un usuari
    $app->get('/usuari/:id', function ($id) use ($app, $dbm) {
        $xml = $dbm->getComentarisFromUsuari($id);
        $app->view->setData(array(
                        'data'     => $app->serializer->getSerialized($xml, 'llocs'),
                        'encoding' => ENCODING)
        );
        $app->render($app->template);
        $app->response->setStatus(200);
    });

    // Comentaris corresponents a un lloc
    $app->get('/lloc/:id', function ($id) use ($app, $dbm) {
        $xml = $dbm->getComentarisFromLloc($id);
        $app->view->setData(array(
                        'data'     => $app->serializer->getSerialized($xml, 'llocs'),
                        'encoding' => ENCODING)
        );
        $app->render($app->template);
        $app->response->setStatus(200);
    });
});
