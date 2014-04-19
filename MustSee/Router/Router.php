<?php
namespace MustSee\Router;

use MustSee\Database\DataBaseManager;
use Serializer\SerializerFactory;
use Slim\Slim;

class Router {
    private $app;
    private $dbm;
    private $serializer;
    private $template;
    private $formats;
    private $charSet = 'utf-8'; // Valor per defecte

    private $format;

    public function __construct(DataBaseManager $dbm, Slim $app) {
        $this->dbm = $dbm;
        $this->app = $app;
    }

    public function run() {
        $this->format = $this->getFormat();
        $this->app->view()->setData(array('encoding' => $this->charSet));
        $this->setResponse();
        $this->processRoute();
        $this->app->run();

    }

    public function setFormats(array $formats) {
        $this->formats = $formats;
    }

    public function setCharset($charSet) {
        $this->charSet = $charSet;
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
        $this->app->response()->header('Content-Type', $this->formats[$this->format] . ';
        charset=' . $this->charSet);
        $this->serializer = SerializerFactory::getInstance($this->format);
        $this->template   = 'template' . strtoupper($this->format) . ".php";
    }

    private function processRoute() {
        // require '../../routes/v2.php';
        $this->app->response->setStatus(200); // Si no hi ha cap problema el resultat serà aquest.

        $this->app->get('/v2/:llocs', array($this, 'noParams'), array($this, 'getLlocs'))
                ->conditions(array('llocs' => 'llocs.\w+'));

        $this->app->get('/v2/llocs/:id', array($this, 'getLloc'))
                ->conditions(array('id' => '[\d]+\..*'));

        $this->app->get('/v2/:categories', array($this, 'noParams'), array($this, 'getCategories'))
                ->conditions(array('categories' => 'categories.\w+'));


        $this->app->get('/:error+', array($this,
                'exceptionNotFound'));
    }

    function setRender($data, $default_node) {
        $this->app->view()->setData(array(
                        'data'     => $this->serializer->getSerialized($data, $default_node),
                        'encoding' => $this->charSet)
        );
        $this->app->render($this->template);
    }

    /* AIXO ES POT POSAR EN EL REQUIRE */
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

    // Middleware
    public function noParams(\Slim\Route $route) {
        $params = $route->getParams();
        print_r($params);

        if (count($params) > 1 || strpos(array_shift($params), '/') !== false) {
            // Conte una barra o hi ha més d'un paràmetre, no es vàlid
            $this->app->redirect('/api/error');
        }
    }

    public function exceptionNotFound() {
        $this->app->response->setStatus(400);
        $data = "Error, no s'ha trobat el recurs.";
        $this->setRender($data, 'error');
    }
}





