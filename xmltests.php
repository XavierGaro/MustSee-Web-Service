<?php
require_once 'classes/database/DatabaseManager.php';
require_once 'classes/data/Categoria.php';
require_once 'classes/data/Lloc.php';
require_once 'classes/data/Imatge.php';
require_once 'classes/data/Comentari.php';
require_once 'classes/data/Usuari.php';
require_once 'classes/serializer/XMLSerializer.php';

$dbm = DataBaseManager::getInstance('MySQL');

header('Content-type: application/xml;charset=iso-8859-1');

//header('Content-type: application/json');

$lloc = array ($dbm->getLloc(5), $dbm ->getLloc(3));

$xml = $lloc;

//$xml = array ("paco", "pedro", "ramirez", new Categoria("playas"));

//$xml = "caracolas";


echo XMLSerializer::getValidXML($xml, 'llocs');