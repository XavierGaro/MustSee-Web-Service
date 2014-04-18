<?php
require_once 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

//require_once 'MustSee/Database/DatabaseManager.php';
//$dbm = MustSee\Database\DataBaseManager::getInstance('MySQL');



//require_once 'Serializer/SerializerFactory.php';
//require_once 'Serializer/XMLSerializer.php';
//require_once 'Serializer/Serializer.php';

/*
require_once 'MustSee/Data/Categoria.php';
require_once 'MustSee/Data/Lloc.php';
require_once 'MustSee/Data/Imatge.php';
require_once 'MustSee/Data/Comentari.php';
require_once 'MustSee/Data/Usuari.php';

require_once 'Serializer/XMLSerializer.php';*/


/*
header('Content-type: application/xml;charset=iso-8859-1');

//header('Content-type: application/json');

$lloc = array ($dbm->getLloc(5), $dbm ->getLloc(3));

$xml = $lloc;

//$xml = array ("paco", "pedro", "ramirez", new Categoria("playas"));




$serializer = new \Serializer\XMLSerializer();

*/
$xml = "caracolas";
$serializer = \Serializer\SerializerFactory::getInstance('xml');
echo $serializer->getSerialized($xml, 'llocs');
