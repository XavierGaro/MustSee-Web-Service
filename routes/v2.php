<?php
// TODO: Comprovar si el resultat de la base de dades es buit i en aquest cas retornar status 404.

$this->app->get('/v2/llocs/', array($this, 'getLlocs'));
//$this->app->get('/v2/llocs/', array($this, 'getLloc'));


// Informació dels llocs
/*
$this->app->group('/llocs', function ()  {

    // El lloc corresponen a la id
    $this->app->get('/:id', function ($id) {
        $data = $this->dbm->getLloc($id);
        $this->app->view->setData(array(
                        'data'     => $this->serializer->getSerialized($data, 'llocs'),
                        'encoding' => ENCODING)
        );
        $this->app->render($this->template);
        $this->app->response->setStatus(200);
    });

    // Tots els llocs
    $this->app->get('/', function ()  {

        $data = $this->dbm->getLlocs();
        $this->app->view->setData(array(
                        'data'     => $this->serializer->getSerialized($data, 'llocs'),
                        'encoding' => ENCODING)
        );
        $this->app->render($this->template);
        $this->app->response->setStatus(200);
    });
});

// Informació de les categories
$app->get('/categories/', function ()  {
    $data = $this->dbm->getCategories();
    $this->app->view->setData(array(
                    'data'     => $this->serializer->getSerialized($data, 'categories'),
                    'encoding' => ENCODING)
    );
    $this->app->render($this->template);
    $this->app->response->setStatus(200);
});

// Comentaris
$this->app->group('/comentaris', function ()  {
    // Comentaris corresponents a un usuari
    $this->app->get('/usuari/:id', function ($id)  {
        $data = $this->dbm->getComentarisFromUsuari($id);
        $this->app->view->setData(array(
                        'data'     => $this->serializer->getSerialized($data, 'llocs'),
                        'encoding' => ENCODING)
        );
        $this->app->render($this->template);
        $this->app->response->setStatus(200);
    });

    // Comentaris corresponents a un lloc
    $this->app->get('/lloc/:id', function ($id) {
        $data = $this->dbm->getComentarisFromLloc($id);
        $this->app->view->setData(array(
                        'data'     => $this->serializer->getSerialized($data, 'llocs'),
                        'encoding' => ENCODING)
        );
        $this->app->render($this->template);
        $this->app->response->setStatus(200);
    });
});
*/
