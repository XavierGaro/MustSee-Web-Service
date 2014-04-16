<?php
require_once 'classes/database/DatabaseManager.php';
require_once 'classes/data/Categoria.php';
require_once 'classes/data/Lloc.php';
require_once 'classes/data/Imatge.php';
require_once 'classes/data/Comentari.php';
require_once 'classes/data/Usuari.php';
require_once 'classes/util/XMLSerializer.php';

$dbm = DataBaseManager::getInstance('MySQL');

echo "<h1>Categories</h1>";
$categories = $dbm->getCategories();
foreach ($categories as $categoria) {
    echo "{$categoria->getId()} - {$categoria->getDescripcio()} <br>";
}

echo "<h1>Llocs</h1>";
$llocs = $dbm->getLlocs();
foreach ($llocs as $lloc) {
    echo "{$lloc->getId()} - {$lloc->getNom()} ({$lloc->getLatitud()}, {$lloc->getLongitud()}). {$lloc->getDescripcio()}<br>";
}

echo "<h1>Detall del lloc 5</h1>";
$lloc = $dbm->getLloc(5);
echo "{$lloc->getId()} - {$lloc->getNom()} ({$lloc->getLatitud()}, {$lloc->getLongitud()}). {$lloc->getDescripcio()}<br>";

echo "<h1>Imatges del lloc 5</h1>";
$imatges = $dbm->getImatgesFromLloc(5);
foreach ($imatges as $imatge) {
    echo "{$imatge->getId()} - {$imatge->getTitol()} <br>";
    echo "<img src=\"{$imatge->getUrl()}\" /> <br>";
}

echo "<h1>Imatge 5</h1>";
$imatge = $dbm->getImatge(5);
echo "{$imatge->getId()} - {$imatge->getTitol()} <br>";
echo "<img src=\"{$imatge->getUrl()}\" /> <br>";


echo "<h1>Comentaris del lloc 1</h1>";
$comentaris = $dbm->getComentarisFromLloc(1);
foreach ($comentaris as $comentari) {
    echo "{$comentari->getId()} - {$comentari->getUsuariId()} : {$comentari->getText()} <br>";
}

echo "<h1>Comentaris del usuari 1</h1>";
$comentaris = $dbm->getComentarisFromUsuari(1);
foreach ($comentaris as $comentari) {
    echo "{$comentari->getId()} - {$comentari->getUsuariId()} : {$comentari->getText()} <br>";
}

echo "<h1>Dades del usuari 1</h1>";
$usuari= $dbm->getUsuari(1);
echo "{$usuari->getId()} - {$usuari->getNom()} : {$usuari->getCorreu()} <br>";


echo "<h1>Comprovar Contrasenya</h1>";
if ($dbm->comprovarContrasenya('testxavi@hotmail.com', '123456')=== true) { // Correcta
    echo "CORRECTE<br>";
} else {
    echo "INCORRECTE<br>";
}

if ($dbm->comprovarContrasenya('testxavi@hotmail.com', '111111')=== true) { // Incorrecta
    echo "CORRECTE<br>";
} else {
    echo "INCORRECTE<br>";
}

if ($dbm->comprovarContrasenya('testxavi@hotmail.es', '123456')=== true) { // Incorrecta
    echo "CORRECTE<br>";
} else {
    echo "INCORRECTE<br>";
}


//$xml = XMLSerializer::generateValidXmlFromObj($usuari);
//echo "xml: $xml del usuari {$usuari->getNom()}";
