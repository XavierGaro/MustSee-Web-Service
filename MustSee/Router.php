<?php

namespace MustSee;


use MustSee\Database\DataBaseManager;
use Slim\Slim;

class Router {
    private $app;
    private $dbm;
    private $formats;

    public function __construct(DataBaseManager $dbm, Slim $app) {
        $this->dbm = $dbm;
        $this->app  = $app;
    }

    public function run() {




        $this->app->run();

    }


    private function getFormat() {
        // Comprovem si s'ha especificat una extensió a la ruta
        $path = $this->app->request->getPath();

        // Comprovem si hi ha un punt
        $dotPos = strpos($path, '.');
        $format = substr($path, $dotPos + 1);

        // Comprovem que no hi hagi cap barra, i es trobi al array de formats.
        if ($format !== false && strstr($format, '/') == false && array_key_exists($format,
                        $this->formats)) {
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
} 