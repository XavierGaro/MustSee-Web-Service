<?php

/**
 * Class XMLSerializer
 * Classe que permet convertir objectes, primitives i arrays en cadenes de text en format XML.
 * Pot generar tant el codi XML per objectes individuals com l'arxiu vàlid amb capçalera.
 * Primer obté totes les propietats amb getter, i després comprova totes les propietats publicas amb getter o sense
 * getter, que no hagi estat ja afegides en el pas anterior.
 *
 * @author Xavier García
 */
class XMLSerializer {
    /**
     * Retorna una cadena de codi XML amb totes les dades del objecte o array passat com argument.
     * @param $object objecte que volem convertir en XML.
     * @param string $root nom del element arrél que es farà servir en cas de que es tracti d'un array.
     * @param string $encoding codificació de la pàgina
     * @return string amb l'objecte convertit en XML
     */
    public static function getValidXML($object, $root = 'root', $encoding = 'iso-8859-1') {
        $xml = "<?xml version=\"1.0\" encoding=\"$encoding\" ?>";
        $xml .= self::getXML($object, $root);
        return $xml;
    }

    public static function getXML($object, $root = 'root') {
        if (is_array($object)) {
            return self::getXMLfromArray($object, $root);
        } else if (is_object($object)) {
            return self::getXMLfromObject($object);
        } else {
            return self::getXMLFromPrimitive($object);
        }
    }

    private static function getXMLfromArray(array $array, $node = 'node') {
        // Recorrem tots els elements del array
        $xml = "<$node>";

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $xml . self::getXMLfromArray($value, $node);
            } else if (is_object($value)) {
                $xml .= self::getXMLfromObject($value);
            } else {
                $xml .= self::getXMLFromPrimitive($value, $key);
            }
        }
        $xml .= "</$node>";
        return $xml;
    }

    // TODO: Refactoritzar per crear un array amb els parells name / value tant dels mètodes com dels atributs i després
    // recórrer-los
    private static function getXMLfromObject($object) {
        // Obtenim la classe reflectida
        $reflect = new ReflectionClass($object);

        // Obrim la etiqueta del node
        $class_name = strtolower($reflect->getName());
        $xml = "<$class_name>";

        // Recorrem tots els mètodes de la classe per obtenir els getters als atributs
        $methods = $reflect->getMethods();
        $atributs_trobats = array();

        foreach ($methods as $method) {
            $method_name = $method->name;
            if (self::isGetter($reflect, $method_name) !== true) {
                // si no es un getter continuem
                continue;
            }
            $method_name = self::retallaGet($method_name);

            // Afegim el atribut a la llista de propietats trobat
            array_push($atributs_trobats, $method_name);

            // Comprovem de quina classe es el valor i posem el nom en minúscules
            $method_value = $method->invoke($object);
            $method_name = strtolower($method_name);

            if (is_array($method_value)) {
                // Recorrem tots els elements del array
                $xml .= self::getXMLfromArray($method_value, $method_name);
            } else if (is_object($method_value)) {
                // Obtenim l'objecte
                $xml .= self::getXMLfromObject($method_value);
            } else {
                // Afegim el node
                $xml .= "<$method_name>";
                $xml .= "$method_value";
                $xml .= "</$method_name>";
            };
        }

        $propietats_accessibles = get_object_vars($object);
        foreach ($propietats_accessibles as $name => $value) {
            if (in_array($name, $atributs_trobats)) {
                // ja l'hem processat
                continue;
            }

            if (is_array($value)) {
                // Recorrem tots els elements del array
                $xml .= self::getXMLfromArray($value, $name);
            } else if (is_object($value)) {
                // Obtenim l'objecte
                $xml .= self::getXMLfromObject($value);
            } else {

                // Afegim el node
                $xml .= "<$name>";
                $xml .= "$value";
                $xml .= "</$name>";
            };

        }


        // Tanquem el node
        $xml .= "</$class_name>";

        return $xml;
    }

    private static function isGetter($reflect, $method_name) {
        // Comprovem si es un getter
        $pattern = '/^get.+/';

        if (preg_match($pattern, $method_name)) {
            // Comprovem si hi ha una propietat amb aquest nom
            if ($reflect->getProperty(self::retallaGet($method_name)) === null) {
                // No Existeix
            } else {
                // Existeix
                return true;
            }
        }
        return false;
    }


    // Eliminem el get i posem el primer caràcter en minúscules

    private static function retallaGet($method_name) {
        $method_name = substr_replace($method_name, '', 0, 3);
        $method_name = substr_replace($method_name, strtolower($method_name[0]), 0, 1);
        return $method_name;
    }

    // Conté un valor primitiu
    private static function getXMLFromPrimitive($value, $node = 'node') {
        if (is_numeric($node)) {
            $node = 'node';
        }
        $value = htmlspecialchars($value, ENT_QUOTES);
        return "<$node>$value</$node>";
    }
}
