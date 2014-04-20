<?php
namespace MustSee\Router;

use Serializer\Serializer;
use Serializer\SerializerFactory;
use Slim\Slim;

class RouteManager {
    const RESOURCE_INT = '[\d]+.*';

    const NOT_FOUND_MSG  = "Error, no s'ha trobat el recurs.";
    const NOT_FOUND_CODE = 400;

    const INVALID_FORMAT_MSG  = "No es reconeix el format demanat.";
    const INVALID_FORMAT_CODE = 500;

    /** @var \Slim\Slim */
    private $app;

    /** @var Serializer */
    private $serializer;

    private $template;
    private $formats;

    private $format;

    public $routes;

    public function __construct(Slim $app) {
        $this->app     = $app;
        $this->routes  = array();
        $this->formats = SerializerFactory::getFormats();
    }

    public function run() {
        try {
            $this->getAcceptedFormat();
            $this->setResponse();
            $this->processRoutes();
            $this->app->run();
        } catch (\Slim\Exception\Stop $e) {
            // S'ha produït una excepció d'aturada, es el comportament esperat del framework
        } catch (\Slim\Exception\Pass $e) {
            // S'ha produït una excepció d'aturada, es el comportament esperat del framework
        }
    }

    private function getAcceptedFormat() {
        // Comprovem si s'ha especificat una extensió a la ruta
        $path = $this->app->request->getPath();

        // Comprovem si hi ha un punt
        $dotPos = strpos($path, '.');
        $format = substr($path, $dotPos + 1);

        // Comprovem que no hi hagi cap barra
        if ($format !== false && strstr($format, '/') == false) {
            // Comprovem que sigui un format vàlid
            if (array_key_exists($format, $this->formats)) {
                $this->format = $format;
            }
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

            // En aquest put no hi ha response?
            $this->app->response->headers()->set('Content-Type', 'application/json');
            $this->setResponse();
            /* Afegim les dades de la capçalera, com que no s'executa Slim::run() l'objecte
             response no s'envia correctament*/
            header('HTTP/1.0 500 Unknown Format');
            header("Content-type: {$this->formats[$this->format]}; charset=utf-8");
            $this->renderError(self::INVALID_FORMAT_MSG, self::INVALID_FORMAT_CODE);
            echo "hola";
        }
    }

    private function setResponse() {
        $this->app->response->headers->set('Content-Type', $this->formats[$this->format] . ";
        charset=utf-8");
        $this->serializer = SerializerFactory::getInstance($this->format);
        $this->template   = 'template' . strtoupper($this->format) . ".php";
    }

    private function processRoutes() {
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
        $this->renderError(self::NOT_FOUND_MSG, self::NOT_FOUND_CODE);
    }

    // Mostra l'error i atura la execució
    public function renderError($missatge, $codi) {
        $this->app->response->setStatus($codi);
        $this->render(['error' => $codi, 'message' => $missatge], 'error');
        $this->app->stop($codi);
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





