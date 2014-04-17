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
     * Retorna una cadena de codi XML amb totes les dades del objecte o array passat com argument incloent
     * la capçalera XML amb la codificació especificada.
     * @param mixed $object objecte que volem convertir en XML
     * @param string $root nom del element arrel que es farà servir en cas de que no es tracti d'un objecte
     * @param string $encoding codificació de la pàgina
     * @return string dades en format XML amb capçalera
     */
    public static function getValidXML($object, $root = 'root', $encoding = self::ENCODING) {
        $xml = "<?xml version=\"1.0\" encoding=\"$encoding\" ?>";
        $xml .= self::getXML($object, $root);
        return $xml;
    }

    /**
     * Retorna una cadena de codi XML amb totes les dades del objecte o array passat com argument.
     * @param mixed $value valor del element
     * @param string $node nom del node
     * @return string dades en format XML
     */
    static function getXML($value, $node) {
        // Comprovem quin tipus de valor s'ha rebut
        if (is_array($value)) {
            $xml = self::getXMLFromArray($value, $node);

        } else if (is_object($value)) {
            $xml = self::getXMLFromObject($value);

        } else {
            $xml = self::getXMLFromPrimitive($value, $node);
        }

        return $xml;
    }

    /**
     * Extreu les dades del array i les retorna com XML
     * @param array $array array del que extraiem les dades
     * @param string $group nom que rebrà el grup
     * @return string dades en format XML
     */
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

    /**
     * Retorna una cadena XML amb el nom del node i el valor passat com argument
     * @param mixed $value valor del element
     * @param string $node nom del node
     * @return string dades en format XML
     */
    private static function getXMLFromPrimitive($value, $node) {
        // Sanegem el nom del node i el valor
        self::sanitizeNode($node);
        self::sanitizeValue($value);

        return "<$node>$value</$node>";
    }

    /**
     * Extreu les propietats públiques i les privades que disposin de getter públic del objecte i
     * les retorna en format XML.
     * @param object $object del que volem extreure les dades
     * @return string dades en format XML
     */
    private static function getXMLFromObject($object) {
        // Obtenim el nom de la classe
        $className = strtolower(get_class($object));

        // Obtenim les propietats accessibles
        $properties = get_object_vars($object);

        // Afegim les propietats privades amb getters
        $properties = array_merge($properties, self::extractPrivateProperties($object));

        return self::getXMLFromArray($properties, $className);
    }

    /**
     * Extreu les propietats públiques i les propietats privades que disposin d'un getter públic.
     * En cas de conflicte les propietats amb getter tenen prioritat sobre les propietats sense
     * getter.
     * @param object $object objecte del que volem extreure les dades
     * @return array amb les propietats que s'han pogut extreure amb el format name=>value
     */
    private static function extractPrivateProperties($object) {
        // Reflectim la classe
        $reflect = new ReflectionClass($object);

        // Obtenim els mètodes públics de la classe
        $methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);

        // Recorrem tots els mètodes de la classe per obtenir els getters de les propietats
        foreach ($methods as $method) {
            $name = $method->name;
            if (self::isGetter($reflect, $name) !== true) {
                // Si no es un getter continuem
                continue;
            }

            // Obtenir el nom de la propietat
            $name = self::retallaGet($name);

            // Obtenim el valor
            $value = $method->invoke($object);

            // Afegim el parell al array
            $properties[$name] = $value;
        }

        return $properties;
    }

    /**
     * @param ReflectionClass $reflect classe reflectia on es troba el mètode que volem comprovar.
     * @param $methodName nom del mètode a comprovar
     * @return bool true si es un getter o false en cas contrari
     */
    private static function isGetter(ReflectionClass $reflect, $methodName) {
        $pattern = '/^get.+/';
        if (preg_match($pattern, $methodName)) {
            // Comprovem si hi ha una propietat amb aquest nom
            if ($reflect->getProperty(self::retallaGet($methodName)) !== null) {
                return true;
            }
        }
        return false;
    }

    /**
     * Si el mètode passat com argument començar per 'get' el retalla
     * @param string $methodName no del que volem eliminar el get
     * @return string el nom sense get al principi
     */
    private static function retallaGet($methodName) {
        $pattern = '/^get.+/';
        if (preg_match($pattern, $methodName)) {
            $methodName = substr_replace($methodName, '', 0, 3);
            $methodName = substr_replace($methodName, strtolower($methodName[0]), 0, 1);
            return $methodName;
        }
    }

    /**
     * Si es un número el substitueix pel nom de node per defecte. En qualsevol cas el posa en minúscules.
     * @param $node node que volem sanejar
     */
    private static function sanitizeNode(&$node) {
        if (is_numeric($node)) {
            $node = self::DEFAULT_NODE;
        }
        $node = strtolower($node);
    }

    /**
     * Saneja el valor passat com argument.
     * @param $value valor a sanejar
     */
    private static function sanitizeValue(&$value) {
        $value = htmlspecialchars($value, ENT_QUOTES);
    }
}