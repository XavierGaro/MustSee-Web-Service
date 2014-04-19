<?php
// TODO: Comprovar si el resultat de la base de dades es buit i en aquest cas retornar status 404.

// Informació dels llocs
$app->group('/llocs', function () use ($app, $dbm) {

    // El lloc corresponen a la id
    $app->get('/:id', function ($id) use ($app, $dbm) {
        $data = $dbm->getLloc($id);
        $app->view->setData(array(
                        'data'     => $app->serializer->getSerialized($data, 'llocs'),
                        'encoding' => ENCODING)
        );
        $app->render($app->template);
        $app->response->setStatus(200);
    });

    // Tots els llocs
    $app->get('/', function () use ($app, $dbm) {
        $data = $dbm->getLlocs();
        $app->view->setData(array(
                        'data'     => $app->serializer->getSerialized($data, 'llocs'),
                        'encoding' => ENCODING)
        );
        $app->render($app->template);
        $app->response->setStatus(200);
    });
});

// Informació de les categories
$app->get('/categories/', function () use ($app, $dbm) {
    $data = $dbm->getCategories();
    $app->view->setData(array(
                    'data'     => $app->serializer->getSerialized($data, 'categories'),
                    'encoding' => ENCODING)
    );
    $app->render($app->template);
    $app->response->setStatus(200);
});

// Comentaris
$app->group('/comentaris', function () use ($app, $dbm) {
    // Comentaris corresponents a un usuari
    $app->get('/usuari/:id', function ($id) use ($app, $dbm) {
        $data = $dbm->getComentarisFromUsuari($id);
        $app->view->setData(array(
                        'data'     => $app->serializer->getSerialized($data, 'llocs'),
                        'encoding' => ENCODING)
        );
        $app->render($app->template);
        $app->response->setStatus(200);
    });

    // Comentaris corresponents a un lloc
    $app->get('/lloc/:id', function ($id) use ($app, $dbm) {
        $data = $dbm->getComentarisFromLloc($id);
        $app->view->setData(array(
                        'data'     => $app->serializer->getSerialized($data, 'llocs'),
                        'encoding' => ENCODING)
        );
        $app->render($app->template);
        $app->response->setStatus(200);
    });
});
