<?php

class XMLSerializerMockObject {
    public $publicProperty = "propietat pública";
    public $publicPropertyWithGetter = 'aquesta no';
    private $privateProperty;
    private $privateArray;
    private $privateObject;


    function getPublicPropertyWithGetter() {
        return "aquesta si";
    }

    function getPrivateProperty() {
        return "propietat privada";
    }

    function getPrivateArray() {
        return array ("one", "two", 42);
    }

    function getPrivateObject() {
        return new stdClass();
    }
}
