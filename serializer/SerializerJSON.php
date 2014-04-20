<?php
namespace Serializer;

/**
 * Class SerializerJSON
 * Classe que permet convertir objectes, primitives i arrays en cadenes de text en format JSON. No
 * requereix el nom del node perquè tota la lògica la pren de SerializerXML.
 *
 * @author  Xavier García
 * @package Serializer
 */
class SerializerJSON implements Serializer {

    /**
     * Retorna les dades seriades en format JSON. Aquesta classe fa servir un altre Serializer de
     * tipus XML per fer una conversió directa. Es requereix PHP 5.4 o superior per que funcioni
     * correctament. Es possible fer el mateix amb versions inferiors però requereix més codi.
     *
     * @param mixed|mixed[] $data dades per seriar
     * @return string dades seriades
     */
    public function getSerialized($data) {
        $serializer = SerializerFactory::getInstance('xml');
        $xml        = $serializer->getSerialized($data);

        return json_encode(simplexml_load_string($xml), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES);
    }
}