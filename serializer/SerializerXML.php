<?php
namespace Serializer;

/**
 * Class SerializerXML
 * Classe que permet convertir objectes, primitives i arrays en cadenes de text en format XML. En
 * tots els casos en que es necessita fer servir un nom de node, si no s'ha passat cap es farà
 * servir el valor per defecte de SerializerFactory.
 *
 * La obtenció de les dades es fa de manera recursiva, així que recupera tots els objectes o arrays
 * encara que estiguin niuats.
 *
 * @author  Xavier García
 * @package Serializer
 */
class SerializerXML implements Serializer {

    /**
     * Aquest mètode retorna les dades passades com arguments convertides en XML.
     *
     * @param mixed|mixed[] $data dades per transformar en format XML
     * @param string        $node nom del node per defecte per si es necessary
     * @return string dades en format XML
     */
    public function getSerialized($data, $node = SerializerFactory::DEFAULT_NODE) {
        // Comprovem quin tipus de valor s'ha rebut
        if (is_array($data)) {
            $xml = $this->getXMLFromArray($data, $node);
        } else
            if (is_object($data)) {
                $xml = $this->getXMLFromObject($data);

            } else {
                $xml = $this->getXMLFromPrimitive($data, $node);
            }

        return $xml;
    }

    /**
     * Extreu les dades del array i les retorna com XML
     *
     * @param mixed[] $array array del que extraiem les dades
     * @param string  $group nom que rebrà el grup
     * @return string dades en format XML
     */
    private function getXMLFromArray(array $array, $group) {
        // Sanegem el nom del grup
        $this->sanitizeNode($group);

        $xml = "<$group>";
        foreach ($array as $node => $value) {
            $xml .= $this->getSerialized($value, $node);
        }
        $xml .= "</$group>";

        return $xml;
    }

    /**
     * Retorna una cadena XML amb el nom del node i el valor passat com argument
     *
     * @param mixed  $value valor del element
     * @param string $node  nom del node
     * @return string dades en format XML
     */
    private function getXMLFromPrimitive($value, $node) {
        // Sanegem el nom del node i el valor
        $this->sanitizeNode($node);
        $this->sanitizeValue($value);

        return "<$node>$value</$node>";
    }

    /**
     * Extreu les propietats públiques i les privades que disposin de getter públic del objecte i
     *  les retorna en format XML.
     *
     * @param object $object del que volem extreure les dades
     * @return string dades en format XML
     */
    private function getXMLFromObject($object) {
        // Obtenim el nom de la classe
        $className = $this->getClassName($object);

        // Obtenim les propietats accessibles
        $properties = get_object_vars($object);

        // Afegim les propietats privades amb getters
        $properties = array_merge($properties, $this->extractPrivateProperties($object));

        return $this->getXMLFromArray($properties, $className);
    }

    /**
     * Retorna el nom de la classe sense el namespace
     *
     * @param object $object objecte del que volem obtenir el nom
     * @return string nom de la classe
     */
    private function getClassName($object) {
        $className = strtolower(get_class($object));
        if (strpos($className, '\\') === false) {
            return $className;
        } else {
            $pos = strrpos($className, '\\');

            return substr($className, $pos + 1);
        }
    }

    /**
     * Extreu les propietats públiques i les propietats privades que disposin d'un getter públic.
     * En cas de conflicte les propietats amb getter tenen prioritat sobre les propietats sense
     * getter.
     *
     * @param object $object objecte del que volem extreure les dades
     * @return array amb les propietats que s'han pogut extreure amb el format name=>value
     */
    private function extractPrivateProperties($object) {
        $properties = array();

        // Reflectim la classe
        $reflect = new \ReflectionClass($object);

        // Obtenim els mètodes públics de la classe
        $methods = $reflect->getMethods(\ReflectionMethod::IS_PUBLIC);

        // Recorrem tots els mètodes de la classe per obtenir els getters de les propietats
        foreach ($methods as $method) {
            $name = $method->name;
            if ($this->isGetter($reflect, $name) !== true) {
                // Si no es un getter continuem
                continue;
            }

            // Obtenir el nom de la propietat
            $name = $this->retallaGet($name);

            // Obtenim el valor
            $value = $method->invoke($object);

            // Afegim el parell al array
            $properties[$name] = $value;
        }

        return $properties;
    }

    /**
     * Comprova si el nom del mètode que s'ha passat com argument es un getter de la classe
     * reflectida.
     *
     * @param \ReflectionClass $reflect    classe reflectia on es troba el mètode que volem
     *                                     comprovar.
     * @param string           $methodName nom del mètode a comprovar
     * @return bool true si es un getter o false en cas contrari
     */
    private function isGetter(\ReflectionClass $reflect, $methodName) {
        // Comprovem si comença per get
        $pattern = '/^get.+/';
        if (preg_match($pattern, $methodName)) {
            // Comprovem si hi ha una propietat amb aquest nom
            if ($reflect->getProperty($this->retallaGet($methodName)) !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Si el mètode passat com argument començar per 'get' el retalla
     *
     * @param string $methodName no del que volem eliminar el get
     * @return string el nom sense get al principi
     */
    private function retallaGet($methodName) {
        $pattern = '/^get.+/';
        if (preg_match($pattern, $methodName)) {
            $methodName = substr_replace($methodName, '', 0, 3);
            $methodName = substr_replace($methodName, strtolower($methodName[0]), 0, 1);
        }

        return $methodName;
    }

    /**
     * Si es un número el substitueix pel nom de node per defecte. En qualsevol cas el posa en
     * minúscules.
     *
     * @param string $node node que volem sanejar
     */
    private function sanitizeNode(&$node) {
        if (is_numeric($node)) {
            $node = SerializerFactory::DEFAULT_NODE;
        }
        $node = strtolower($node);
    }

    /**
     * Saneja el valor passat com argument.
     *
     * @param string $value valor a sanejar
     */
    private function sanitizeValue(&$value) {
        $value = htmlspecialchars($value, ENT_QUOTES);
    }
}