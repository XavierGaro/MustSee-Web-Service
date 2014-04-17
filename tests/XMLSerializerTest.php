<?php
require_once '../classes/serializer/XMLSerializer.php';

/**
 * Class XMLSerializerTest
 *
 * @author Xavier García
 */
class XMLSerializerTest extends PHPUnit_Framework_TestCase {

    public function testGetXMLString() {
        $string   = 'prova';
        $expected = "<test>prova</test>";
        $this->assertEquals($expected, XMLSerializer::getXML($string, 'test'));
    }

    public function testGetXMLNumber() {
        $number   = 42;
        $expected = '<test>42</test>';
        $this->assertEquals($expected, XMLSerializer::getXML($number, 'test'));
    }

    public function testGetXMLArray() {
        $array    = array("one", "two", "three");
        $expected = '<test><node>one</node><node>two</node><node>three</node></test>';
        $this->assertEquals($expected, XMLSerializer::getXML($array, 'test'));
    }

    public function testGetXMLObject() {
        $object   = new XMLSerializerMockObject();
        $expected = '<xmlserializermockobject><publicproperty>propietat pública</publicproperty>'
                . '<publicpropertywithgetter>aquesta si</publicpropertywithgetter><privateproperty>'
                . 'propietat privada</privateproperty><privatearray><node>one</node><node>two</node>'
                . '<node>42</node></privatearray><stdclass></stdclass></xmlserializermockobject>';
        $this->assertEquals($expected, XMLSerializer::getXML($object));
    }

    public function testGetValidXML() {
        $xml      = 'prova';
        $expected = "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?><test>prova</test>";
        $this->assertEquals($expected, XMLSerializer::getValidXML($xml, 'test'));
    }
}

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