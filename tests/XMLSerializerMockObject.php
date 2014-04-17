<?php

/**
 * Class XMLSerializerMockObject
 * Aquesta classe serveix per comprovar que XMLSerializer funciona correctament. Només incorpora
 * les propietats i mètodes necessaries per fer la prova d'objectes.
 *
 * @author Xavier García
 */
class XMLSerializerMockObject {
    public $publicProperty = 'propietat pública';
    public $publicPropertyWithGetter = 'aquesta no';
    private $privateProperty = 'propietat privada';
    private $privateArray;
    private $privateObject;


    function getPublicPropertyWithGetter() {
        return "aquesta si";
    }

    function getPrivateProperty() {
        return $this->privateProperty;
    }

    function getPrivateArray() {
        return array("one", "two", 42);
    }

    function getPrivateObject() {
        return new stdClass();
    }
}
