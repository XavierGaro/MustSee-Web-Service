<?php
namespace Serializer;

/**
 * Class SerializerFactory
 * Aquesta classe es una factoria per crear objectes que implementen la interfície Serializer.
 *
 * @author  Xavier García
 * @package Serializer
 */
class SerializerFactory {
    const DEFAULT_NODE = 'node';

    private static $formats = array(
            'xml'  => 'application/xml',
            'json' => 'application/json'
    );

    /**
     * No es pot instanciar aquesta classe.
     */
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
                return new SerializerXML($default_node);

            case 'json':
                return new SerializerJSON($default_node);

            default:
                throw new \Exception("Error, no es pot serialitzar a aquest format: $format");
        }
    }

    /**
     * Retorna un array associatiu amb els formats suportats per aquesta factoria.
     *
     * @return string[] array associatiu amb els formats en que es poden seriar les dades amb la
     * estructura similar a 'xml' => 'application/xml'.
     */
    public static function getFormats() {
        return self::$formats;
    }

    /**
     * Retorna el primer element del array associatiu que emmagatzema els formats.
     *
     * @return String[] array del tipus ('xml' => 'application/xml')
     */
    public static function getDefault() {
        reset(self::$formats);

        return key(self::$formats);
    }
}