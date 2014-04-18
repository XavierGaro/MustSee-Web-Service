<?php

namespace Serializer;

class JSONSerializer implements Serializer{


    /**
     * Retorna les dades seriades en el format adequat per la classe concreta.
     *
     * @param mixed $data dades per seriar
     * @return string dades seriades
     */
    public function getSerialized($data) {
        $serializer = SerializerFactory::getInstance('xml');
        $xml = $serializer->getSerialized($data);
        return json_encode(simplexml_load_string($xml), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES);
    }
}