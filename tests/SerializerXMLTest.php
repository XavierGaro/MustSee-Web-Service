<?php
use Serializer\Serializer;

require_once 'AutoLoader.php';

/**
 * Class SerializerXMLTest
 * Tests per comprovar el funcionament correcte de la classe SerializerXML.
 *
 * @author Xavier García
 */
class SerializerXMLTest extends PHPUnit_Framework_TestCase {
    const DEFAULT_NODE = 'test';

    /**
     * @var Serializer
     */
    private $serializer;

    protected function setUp() {
        $this->serializer = \Serializer\SerializerFactory::getInstance('xml');
    }

    public function testGetXMLString() {
        $string   = 'prova';
        $expected = "<test>prova</test>";
        $xml      = $this->serializer->getSerialized($string, self::DEFAULT_NODE);
        $this->assertEquals($expected, $xml);
    }

    public function testGetXMLNumber() {
        $number   = 42;
        $expected = '<test>42</test>';
        $xml      = $this->serializer->getSerialized($number, self::DEFAULT_NODE);
        $this->assertEquals($expected, $xml);
    }

    public function testGetXMLArray() {
        $array    = array("one", "two", "three");
        $expected = '<test><node>one</node><node>two</node><node>three</node></test>';
        $xml      = $this->serializer->getSerialized($array, self::DEFAULT_NODE);
        $this->assertEquals($expected, $xml);
    }

    public function testGetXMLObject() {
        $object   = new XMLSerializerMockObject();
        $expected = '<xmlserializermockobject><publicproperty>propietat pública</publicproperty>'
                . '<publicpropertywithgetter>aquesta si</publicpropertywithgetter><privateproperty>'
                . 'propietat privada</privateproperty><privatearray><node>one</node><node>two</node>'
                . '<node>42</node></privatearray><stdclass></stdclass></xmlserializermockobject>';
        $xml      = $this->serializer->getSerialized($object, self::DEFAULT_NODE);
        $this->assertEquals($expected, $xml);
    }
}

/**
 * Class XMLSerializerMockObject
 * Objecte mock per poder realitzar els tests.
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