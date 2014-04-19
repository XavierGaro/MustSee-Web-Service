<?php
namespace MustSee\Router;

class Route {
    public $route;
    public $function;
    public $condition;
    public $node;

    // Pot ser un callable o un array de callables
    public $middleware;

    /** @var  RouteManager */
    private $routeManager;

    public function __construct($route, $function, array $condition, $node = 'node',
                                $middleware = null) {
        $this->route      = $route;
        $this->route      = $route;
        $this->function   = $function;
        $this->condition  = $condition;
        $this->node       = $node;
        $this->middleware = $middleware;
    }

    public function setRouteManager(RouteManager $renderer) {
        $this->routeManager = $renderer;
    }

    public function processRoute($param) {
        $data = call_user_func($this->function, $param);

        // Si les dades son un objecte null o un array buit, mostrem el missatge d'error
        if ($data===null || (is_array($data) && empty($data))) {
            $this->routeManager->renderError();
        } else {
            $this->routeManager->render($data, $this->node);
        }

    }
}