<?php
namespace MustSee\Router;

use MustSee\Database\DataBaseManager;
use Serializer\SerializerFactory;
use Slim\Slim;

class Router {

    const RESOURCE_INT = '[\d]+\..*';

    private $app;
    private $dbm;
    private $serializer;
    private $template;
    private $formats;

    private $format;

    public function __construct(DataBaseManager $dbm, Slim $app) {
        $this->dbm = $dbm;
        $this->app = $app;
    }

    public function run() {
        $this->format = $this->getFormat();
        $this->setResponse();
        $this->processRoute();
        $this->app->run();

    }

    public function setFormats(array $formats) {
        $this->formats = $formats;
    }

    private function getFormat() {
        // Comprovem si s'ha especificat una extensió a la ruta
        $path = $this->app->request->getPath();

        // Comprovem si hi ha un punt
        $dotPos = strpos($path, '.');
        $format = substr($path, $dotPos + 1);

        // Comprovem que no hi hagi cap barra, i es trobi al array de formats.
        if ($format !== false && strstr($format, '/') == false && array_key_exists($format,
                        $this->formats)
        ) {
            return $format;
        }

        // Si no s'ha trobat cap format vàlid comprovem el tipus de fitxer acceptat
        $accept = $this->app->request->headers->get('Accept');
        foreach ($this->formats as $format => $contentType) {
            if (stripos($accept, $contentType) !== false) {
                return $format;
            }
        }

        // Si no s'ha trobat cap tipus ni com a extensió ni com acceptat llencem una excepció
        throw new \Exception ("No es reconeix cap tipus de resposta");
    }

    private function setResponse() {
        $this->app->response()->header('Content-Type', $this->formats[$this->format] . ";charset=utf-8");
        $this->serializer = SerializerFactory::getInstance($this->format);
        $this->template   = 'template' . strtoupper($this->format) . ".php";
    }

    private function processRoute() {
        $this->app->response->setStatus(200); // Si no hi ha cap problema el resultat serà aquest.

        $this->app->get('/v1/:llocs', array($this, 'noParams'), array($this, 'getLlocs'))
                ->conditions(array('llocs' => 'llocs.\w+'));

        $this->app->get('/v1/llocs/:id', array($this, 'getLloc'))
                ->conditions(array('id' => self::RESOURCE_INT));

        $this->app->get('/v1/:categories', array($this, 'noParams'), array($this, 'getCategories'))
                ->conditions(array('categories' => 'categories.\w+'));

        $this->app->get('/v1/comentaris/usuari/:id', array($this, 'getComentarisFromUsuari'))
                ->conditions(array('id' => self::RESOURCE_INT));

        $this->app->get('/v1/comentaris/llocs/:id', array($this, 'getComentarisFromLlocs'))
                ->conditions(array('id' => self::RESOURCE_INT));

        $this->app->get('/:error+', array($this,
                'exceptionNotFound'));
    }

    function setRender($data, $default_node) {
        $this->app->view()->setData(array(
                        'data' => $this->serializer->getSerialized($data, $default_node))
        );
        $this->app->render($this->template);
    }

    public function getLlocs() {
        $data = $this->dbm->getLlocs();
        $this->setRender($data, 'llocs');
    }

    public function getLloc($id) {
        $data = $this->dbm->getLloc($id);
        $this->setRender($data, 'llocs');
    }

    public function getCategories() {
        $data = $this->dbm->getCategories();
        $this->setRender($data, 'categories');
    }

    public function getComentarisFromUsuari($id) {
        $data = $this->dbm->getComentarisFromUsuari($id);
        $this->setRender($data, 'comentaris');
    }

    public function getComentarisFromLlocs($id) {
        $data = $this->dbm->getComentarisFromLloc($id);
        $this->setRender($data, 'comentaris');
    }

    // Middleware
    public function noParams(\Slim\Route $route) {
        $params = $route->getParams();
        if (count($params) > 1 || strpos(array_shift($params), '/') !== false) {
            // Conte una barra o hi ha més d'un paràmetre, no es vàlid
            $this->app->pass();
        }
    }

    public function exceptionNotFound() {
        $this->app->response->setStatus(400);
        $data = "Error, no s'ha trobat el recurs.";
        $this->setRender($data, 'error');
    }
}





