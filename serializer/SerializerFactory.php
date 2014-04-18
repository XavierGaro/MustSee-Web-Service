<?php
namespace Serializer;

class SerializerFactory {
    const DEFAULT_NODE = 'node';

    private function __construct() {
    }

    /**
     * @param string $format       amb el que volem seriar les dades
     * @param string $default_node nom del node per defecte en cas de que no es pugui fer servir
     *                             el que s'extreu de les dades.
     * @return Serializer pel format especificat
     * @throws \Exception si no es pot serialitzar en aquest format
     */
    public static function getInstance($format, $default_node = self::DEFAULT_NODE) {
        switch (strtolower($format)) {
            case 'xml':
                return new XMLSerializer($default_node);

            case 'json':
                // TODO: return new JSONSerializer($default_node);

            default:
                throw new \Exception("Error, no es pot serialitzar a aquest format.");
        }
    }
}