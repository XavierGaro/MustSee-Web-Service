<?php
namespace MustSee\Router;

use Serializer\Serializer;
use Serializer\SerializerFactory;
use Slim\Slim;

class RouteManager {

    //const RESOURCE_INT = '[\d]+\..*';
    const RESOURCE_INT = '[\d]+.*';

    const NOT_FOUND_MSG  = "Error, no s'ha trobat el recurs.";
    const NOT_FOUND_CODE = 400;

    /** @var \Slim\Slim */
    private $app;


    /** @var Serializer */
    private $serializer;

    private $template;
    private $formats;

    private $format;

    public $routes;

    private $stopRouting = false;


    public function __construct(Slim $app) {
        $this->app    = $app;
        $this->routes = array();
    }

    public function run() {
        $this->getFormat();
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
            $this->format = $format;

        } else {
            // Si no s'ha trobat cap format vàlid comprovem el tipus de fitxer acceptat
            $accept = $this->app->request->headers->get('Accept');
            foreach ($this->formats as $format => $contentType) {
                if (stripos($accept, $contentType) !== false) {
                    $this->format = $format;
                }
            }
        }

        // Si no s'ha trobat cap tipus ni com a extensió mostrem l'error en el format del primer
        // que tenim a la llista
        if ($this->format === null) {
            reset($this->formats);
            $this->format = key($this->formats);
            $this->setResponse();
            $this->renderError("No s'ha trobat cap format vàlid per tornar les dades", 500);
        }
    }

    private function setResponse() {
        $this->app->response()->header('Content-Type', $this->formats[$this->format] . ";charset=utf-8");
        $this->serializer = SerializerFactory::getInstance($this->format);
        $this->template   = 'template' . strtoupper($this->format) . ".php";
    }

    private function processRoute() {
        if ($this->stopRouting !== true) {
            $this->app->response->setStatus(200); // Si no hi ha cap problema el resultat serà aquest.


            foreach ($this->routes as $r) {
                /** @var $r Route */
                $r->setRouteManager($this);

                switch ($r->verb) {
                    case 'GET':
                        if (is_callable($r->middleware)) {
                            $this->app->get($r->route, $r->middleware, array($r, 'processRoute'))
                                    ->conditions($r->condition);
                        } else {
                            $this->app->get($r->route, array($r, 'processRoute'))
                                    ->conditions($r->condition);
                        }
                        break;

                    case 'POST':
                        if (is_callable($r->middleware)) {
                            $this->app->post($r->route, $r->middleware, array($r, 'processRoute'))
                                    ->conditions($r->condition);
                        } else {
                            $this->app->post($r->route, array($r, 'processRoute'))
                                    ->conditions($r->condition);
                        }
                        break;
                }
            }


            // Rutes comuns
            $this->app->map('/:error+', array($this, 'renderNotFound'))->via('GET', 'POST',
                    'DELETE', 'PUT');

        } else {
            // No es processa cap ruta
            $this->app->map('/:sortir+', array($this, 'sortir'))->via('GET', 'POST',
                    'DELETE', 'PUT');
        }
    }

    function render($data, $default_node) {
        $this->app->view()->setData(array(
                        'data' => $this->serializer->getSerialized($data, $default_node))
        );
        $this->app->render($this->template);
    }

    /* Aquest mètode es cridat com a callback de la ruta /:error+ així que cridem a aquest i
    després a renderError() per poder fer servir els arguments per defecte */
    public function renderNotFound() {
        $this->renderError();

    }

    // Llença una excepció per aturar la execució, es el comportament normal del framework
    public function renderError($missatge = self::NOT_FOUND_MSG, $codi = self::NOT_FOUND_CODE) {
        $this->app->response->setStatus($codi);
        $this->render(['error' => $codi, 'message' => $missatge], 'error');
        //$this->stopRouting = true;
        $this->app->stop($codi);
    }

    public function sortir() {
        // No fem res
    }

    public function addRoute(Route $route) {
        $this->routes[] = $route;
    }

    public function addRoutes(array $routes) {
        $this->routes = array_merge($this->routes, $routes);
    }

    // Middleware
    public function noParams(\Slim\Route $route) {
        $params = $route->getParams();
        if (count($params) > 1 || strpos(array_shift($params), '/') !== false) {
            // Conte una barra o hi ha més d'un paràmetre, no es vàlid
            $this->app->pass();
        }
    }
}





