<?php
namespace MustSee\Router;
/**
 * Class Route
 * Aquesta classe emmagatzema les dades necessaries per crear una ruta que serà processada per la
 * classe RouteManager.
 * S'ha de tenir molt de compte al construir les rutes, ja que les propietats Route::$function i
 * Route::$middleware tracten amb objectes Callable.
 * Normalment el RouteManager s'injectarà a si mateix en el moment de processar la ruta.
 *
 * @todo    si hi haguessin més tipus de rutes s'hauria de extreure la interfície
 * @author  Xavier García
 * @package MustSee\Router
 */
class Route {
    public $verb;
    public $pattern;
    public $function;
    public $condition;
    public $node;
    public $middleware;

    /** @var RouteManager on es processa aquesta ruta */
    private $routeManager;

    /**
     * Construeix una objecte amb tota la informació necessària per mostrar la informació d'una ruta.
     *
     * @param string        $verb       ha de ser GET, POST, PUT o DELETE, es la acció que es vol
     *                                  portar a terme
     * @param string        $pattern    patró al que respon aquesta ruta
     * @param Callable      $function   aquest es el Callable que serà cridat per obtenir les dades
     *                                  a mostrar
     * @param string[]      $condition  es un array associatiu que emmagatzema les condicions extres
     *                                  que ha de complir la ruta. Pot ser buit però no pot ser
     *                                  null. El format esperat per la condició es
     *                                  'param' => 'condició', sent 'param' el nom d'un paràmetre
     *                                  establert al pattern i la 'condició' una expressió regular.
     * @param string        $node       nom per defecte del node en cas de ser necessari al generar
     *                                  la vista de les dades.
     * @param null|Callable $middleware el middleware es un callable que s'executarà abans que la
     *                                  funció principal i es pot fer servir per controlar el
     *                                  nombre de paràmetres, autenticar l'usuari, etc. Callable
     *                                  que s'executaran.
     */
    public function __construct($verb, $pattern, $function, array $condition, $node = 'node',
                                $middleware = null) {
        $this->verb       = $verb;
        $this->pattern    = $pattern;
        $this->function   = $function;
        $this->condition  = $condition;
        $this->node       = $node;
        $this->middleware = $middleware;
    }

    /**
     * Aquest mètode es cridat pel RouteManager per injectar-se a si mateix.
     *
     * @param RouteManager $routeManager
     */
    public function setRouteManager(RouteManager $routeManager) {
        $this->routeManager = $routeManager;
    }

    /**
     * Processa aquesta ruta, crida al callable establert en el constructor passant-li els
     * paràmetres de la ruta. En cas de que el callable retorni null o un objecte buit, es
     * mostrarà la pantalla d'error, i si s'ha trobat alguna cosa es mostrarà per pantalla.
     *
     * @param mixed|mixed[] $param paràmetres de la ruta
     */
    public function processRoute($param) {
        $data = call_user_func($this->function, $param);

        // Si les dades son un objecte null o un array buit, mostrem el missatge d'error
        if ($data === null || (is_array($data) && empty($data))) {
            $this->routeManager->renderError(
                    RouteManager::NOT_FOUND_MSG,
                    RouteManager::NOT_FOUND_CODE
            );
        } else {
            $this->routeManager->render($data, $this->node);
        }
    }
}