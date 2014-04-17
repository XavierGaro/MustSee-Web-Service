<?php

/**
 * Class XMLSerializer
 * Classe que permet convertir objectes, primitives i arrays en cadenes de text en format XML.
 * Pot generar tant el codi XML per objectes individuals com l'arxiu vàlid amb capçalera.
 * Obté totes les propietats públiques i les privades que tinguin un getter dels objectes. El
 * getter ha de seguir el format 'getNomPropietat' exactament per ser reconegut i ha de ser
 * públic.
 *
 * @author Xavier García
 */
class XMLSerializer {
    const ENCODING = 'iso-8859-1';
    const DEFAULT_NODE = 'node';

    /**
     * Retorna una cadena de codi XML amb totes les dades del objecte o array passat com argument.
     * @param $object objecte que volem convertir en XML.
     * @param string $root nom del element arrel que es farà servir en cas de que es tracti d'un array.
     * @param string $encoding codificació de la pàgina
     * @return string amb l'objecte convertit en XML
     */
    public static function getValidXML($object, $root = 'root', $encoding = self::ENCODING) {
        $xml = "<?xml version=\"1.0\" encoding=\"$encoding\" ?>";
        $xml .= self::getXML($object, $root);
        return $xml;
    }

    static function getXML($value, $node) {
        // Comprovem quin tipus de valor s'ha rebut
        if (is_array($value)) {
            // Processem l'array
            $xml = self::getXMLFromArray($value, $node);

        } else if (is_object($value)) {
            // Processem l'objecte
            $xml = self::getXMLFromObject($value);

        } else {
            $xml = self::getXMLFromPrimitive($node, $value);
        }

        return $xml;
    }

    private static function getXMLFromArray(array $array, $group) {
        // Sanegem el nom del grup
        self::sanitizeNode($group);

        $xml = "<$group>";
        foreach ($array as $node => $value) {
            $xml .= self::getXML($value, $node);
        }
        $xml .= "</$group>";
        return $xml;
    }

    private static function getXMLFromPrimitive($node, $value) {
        // Sanegem el nom del node i el valor
        self::sanitizeNode($node);
        self::sanitizeValue($value);

        // Afegim el valor
        $xml = "<$node>$value</$node>";

        return $xml;
    }

    private static function getXMLFromObject($object) {
        // Obtenim el nom de la classe
        $className = strtolower(get_class($object));

        // Obtenim les propietats accessibles
        $properties = get_object_vars($object);

        // Afegim les propietats privades amb getters
        $properties = array_merge($properties, self::extractPrivateProperties($object));

        return self::getXMLFromArray($properties, $className);
    }

    private static function extractPrivateProperties($object) {
        // Reflectim la classe
        $reflect = new ReflectionClass($object);

        // Obtenim els mètodes públics de la classe
        $methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);

        // Recorrem tots els mètodes de la classe per obtenir els getters de les propietats
        foreach ($methods as $method) {
            $name = $method->name;
            if (self::isGetter($reflect, $name) !== true) {
                // si no es un getter continuem
                continue;
            }

            // Eliminem el get per obtenir el nom de la propietat
            $name = self::retallaGet($name);

            // Obtenim el valor
            $value = $method->invoke($object);

            // Afegim el parell al array
            $properties[$name] = $value;
        }

        return $properties;
    }

    // Eliminem el get i posem el primer caràcter en minúscules
    private static function isGetter($reflect, $method_name) {
        $pattern = '/^get.+/';
        if (preg_match($pattern, $method_name)) {
            // Comprovem si hi ha una propietat amb aquest nom
            if ($reflect->getProperty(self::retallaGet($method_name)) !== null) {
                return true;
            }
        }
        return false;
    }
    private static function retallaGet($method_name) {
        $method_name = substr_replace($method_name, '', 0, 3);
        $method_name = substr_replace($method_name, strtolower($method_name[0]), 0, 1);
        return $method_name;
    }

    private static function sanitizeNode(&$node) {
        if (is_numeric($node)) {
            $node = self::DEFAULT_NODE;
        }
        $node = strtolower($node);
    }

    private static function sanitizeValue(&$value) {
        $value = htmlspecialchars($value, ENT_QUOTES);
    }
}