<?php
namespace MustSee\Router;

use Serializer\Serializer;
use Serializer\SerializerFactory;
use Slim\Slim;

/**
 * Class RouteManager
 * Classe per gestionar les possibles rutes i cridar a les funcions i middleware adequades o
 * redirigir cap a les pàgines d'error.
 *
 * Podrà retornar la informació en diferents formats segons la disponibilitat dels objectes
 * generats per la SerializerFactory i els templates adequats.
 *
 * Els templates de cada format tenen el nom fixe format per 'template' més el nom del format en
 * majúscules i la extensió '.php', per exemple 'templateJSON.php'.
 *
 * @todo    si haguessin més tipus de RouteManager s'hauria d'extreure la interfície
 * @author  Xavier García
 * @package MustSee\Router
 */
class RouteManager {
    // Patrons condicionals comuns
    const RESOURCE_INT = '[\d]+(\.{1}[^\\.]*)?';
    const EXTENSION    = '(\.{1}[^\\.]*)?';

    // Missatges d'error
    const NOT_FOUND_MSG  = "Error, no s'ha trobat el recurs.";
    const NOT_FOUND_CODE = 400;

    const INVALID_FORMAT_MSG  = "No es reconeix el format demanat.";
    const INVALID_FORMAT_CODE = 500;

    private $app;

    /** @var  Serializer  */
    private $serializer;
    private $template;

    /** @var \string[]  */
    private $formats;
    /** @var  string */
    private $format;

    /** @var Route[] array amb totes les rutes */
    public $routes;

    /**
     * Al crear l'objecte s'ha de passar una instància del framework Slim. Es generarà un array
     * buit per emmagatzemar les rutes, i s'obtindrà la llista de formats per serialitzar
     * disponibles.
     *
     * @param Slim $app instància del framework.
     */
    public function __construct(Slim $app) {
        $this->app     = $app;
        $this->routes  = array();
        $this->formats = SerializerFactory::getFormats();
    }

    /**
     * Al executar aquest mètode s'inicia la seqüència de preparació de les rutes que segueix els
     * següents passos:
     *      Comprovar el format en que s'ha de servir la informació
     *      Establir la capçalera de resposta i el Serializer adequat
     *      Processar les rutes carregades
     *      Executar el framework Slim
     *
     * @throws \Slim\Exception\Stop|\Slim\Exception\Pass aquestes excepcions son llençades pel
     * framework al cridar als mètodes Slim::stop(), Slim::halt(), Slim::redirect() i Slim::pass().
     * En el nostre cas simplement les ignorem ja que son llençades quan volem aturar la execució
     * després de mostrar la pàgina d'error.
     */
    public function run() {
        try {
            $this->getAcceptedFormat();
            $this->setResponse();
            $this->processRoutes();
            $this->app->run();
        } catch (\Slim\Exception\Stop $e) {
            // S'ha produït una excepció d'aturada, es el comportament esperat del framework
        } catch (\Slim\Exception\Pass $e) {
            // S'ha produït una excepció de passar, es el comportament esperat del framework
        }
    }

    /**
     * Comprovem si s'ha especificat el format com extensió i si es així comprovem si aquesta és
     * vàlida. Si no s'ha especificat cap comprovem els formats de resposta que accepta el client.
     * Si tampoc hi ha cap vàlid mostrem un error i s'atura la execució.
     */
    private function getAcceptedFormat() {
        // Comprovem si s'ha especificat una extensió a la ruta
        $extension = pathinfo($this->app->request->getPath(), PATHINFO_EXTENSION);

        if (strlen($extension) === 0) {
            // No hi ha extensió, busquem per els mètodes acceptats per la capçalera de la petició
            $this->format = $this->checkAcceptedTypes($extension);
        } else {
            // Comprovem si la extensió es vàlida
            $this->format = $this->checkAcceptedExtensions($extension);
        }

        // Si arribats a aquest punt no hem establert el format mostrem l'error
        if ($this->format === null) {
            $this->renderUnknownFormat();
        }
    }

    /**
     * Contrasta els tipus acceptats per la petició amb els acceptats pel SerializerFactory.
     *
     * @return null|string si la petició accepta algun dels tipus permesos retorna el tipus, si no
     * retorna null
     */
    private function checkAcceptedTypes() {
        $accept = $this->app->request->headers->get('Accept');

        // Recorrem els formats acceptables
        foreach ($this->formats as $format => $contentType) {
            // Si es troba alguna coincidència establim el format:
            if (stripos($accept, $contentType) !== false) {
                return $format;
            }
        }

        return null;
    }

    /**
     * Comprova si la extensió demanada correspon a un format vàlid.
     *
     * @param string $extension extensió de la ruta
     * @return null|string la extensió si s'ha trobat o null si no es així
     */
    private function checkAcceptedExtensions($extension) {
        if (array_key_exists($extension, $this->formats)) {
            return $extension;
        } else {
            return null;
        }
    }

    /**
     * Estableix la capçalera, el serializer i el template adequats al format demanat per la petició.
     */
    private function setResponse() {
        $this->app->response->headers->set('Content-Type', $this->formats[$this->format] .
                ";charset=utf-8");
        $this->serializer = SerializerFactory::getInstance($this->format);
        $this->template   = 'template' . strtoupper($this->format) . ".php";
    }

    /**
     * Processa totes les rutes emmagatzemades. Es defineix com a codi de resposta 200 que es el que
     * s'enviarà si no hi ha cap error.
     */
    private function processRoutes() {
        $this->app->response->setStatus(200);

        foreach ($this->routes as $r) {
            $r->setRouteManager($this);

            switch ($r->verb) {
                case 'GET':
                    if (is_callable($r->middleware)) {
                        $this->app
                                ->get($r->pattern, $r->middleware, array($r, 'processRoute'))
                                ->conditions($r->condition);

                    } else {
                        $this->app
                                ->get($r->pattern, array($r, 'processRoute'))
                                ->conditions($r->condition);
                    }
                    break;

                case 'POST':
                    if (is_callable($r->middleware)) {
                        $this->app
                                ->post($r->pattern, $r->middleware, array($r, 'processRoute'))
                                ->conditions($r->condition);
                    } else {
                        $this->app
                                ->post($r->pattern, array($r, 'processRoute'))
                                ->conditions($r->condition);
                    }
                    break;

                case 'PUT':
                    if (is_callable($r->middleware)) {
                        $this->app
                                ->put($r->pattern, $r->middleware, array($r, 'processRoute'))
                                ->conditions($r->condition);
                    } else {
                        $this->app
                                ->put($r->pattern, array($r, 'processRoute'))
                                ->conditions($r->condition);
                    }
                    break;

                case 'DELETE':
                    if (is_callable($r->middleware)) {
                        $this->app
                                ->delete($r->pattern, $r->middleware, array($r, 'processRoute'))
                                ->conditions($r->condition);
                    } else {
                        $this->app
                                ->delete($r->pattern, array($r, 'processRoute'))
                                ->conditions($r->condition);
                    }
                    break;
            }
        }

        // En cas de que no es trobi cap coincidència es mostrarà el missatge d'error
        $this->app
                ->map('/:error+', array($this, 'renderNotFound'))
                ->via('GET', 'POST', 'DELETE', 'PUT');
    }

    /**
     * Aquest mètode serialitza les dades que es passin per argument al format que s'hagi
     * determinat prèviament
     *
     * @param mixed|mixed[] $data         dades que seran serialitzades i mostrades.
     * @param string        $default_node nom del node si no es proporcionat per les dades.
     */
    public function render($data, $default_node) {
        $this->app->view()->setData(array(
                        'data' => $this->serializer->getSerialized($data, $default_node))
        );
        $this->app->render($this->template);
    }


    // Mostra l'error i atura la execució
    /**
     * Aquest mètode es cridat per altres mètodes per mostrar els missatges d'error i el seu codi,
     * realitza una funció similar a RouterManager::render() però pels missatges d'error. També
     * finalitza el processament de rutes llençant una excepció del tipus \Slim\Exception\Stop.
     *
     * @param string $missatge missatge d'error que es mostrarà
     * @param int    $codi     codi d'error que es fara servir com a codi de resposta, com 400 o 500
     */
    public function renderError($missatge, $codi) {
        $this->app->response->setStatus($codi);
        $this->render(['error' => $codi, 'message' => $missatge], 'error');
        $this->app->stop($codi);
    }

    /* Aquest mètode es cridat com a callback de la ruta /:error+ així que cridem a aquest i
després a renderError() per poder fer servir els arguments per defecte */

    /**
     * Aquest mètode es cridat com a Callable en el cas de no trobar-se cap recurs que coincideixi
     * amb la ruta. Ignora tots els paràmetres i mostra el missatge d'error.
     */
    public function renderNotFound() {
        $this->renderError(self::NOT_FOUND_MSG, self::NOT_FOUND_CODE);
    }

    /**
     * Prepara la aplicació per mostrar un missatge de error de format desconegut fent servir el
     * format per defecte del SerializerFactory i una capçalera standard, ja que no es generen les
     * dades de la capçalera de la resposta per Slim perquè no s'arriba a executar el framework.
     */
    private function renderUnknownFormat() {
        // Obtenim el format per defecte
        $this->format = SerializerFactory::getDefault();
        $this->setResponse();

        // Establim les dades de la capçalera
        header('HTTP/1.1 500 Unknown Format');
        header("Content-type: {$this->formats[$this->format]}; charset=utf-8");

        // Mostrem l'error
        $this->renderError(self::INVALID_FORMAT_MSG, self::INVALID_FORMAT_CODE);
    }

    /**
     * Afegeix una ruta al array de rutes.
     *
     * @param Route $route ruta per afegir
     */
    public function addRoute(Route $route) {
        $this->routes[] = $route;
    }

    /**
     * Afegeix un array de rutes al array de rutes.
     *
     * @param Route[] $routes array de rutes per afegir
     */
    public function addRoutes(array $routes) {
        $this->routes = array_merge($this->routes, $routes);
    }
}