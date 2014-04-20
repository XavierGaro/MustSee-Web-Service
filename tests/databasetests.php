<?php
/** Com que el nostre hosting es gratuït no ens permet fer les proves de connexió a la base de
 * dades de forma remota, així que ho hem de comprovar manualment
 *
 * @author Xavier García
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once '../MustSee/Database/DatabaseManager.php';
require_once '../MustSee/Database/PDOFactory.php';
require_once '../MustSee/Data/Categoria.php';
require_once '../MustSee/Data/Comentari.php';
require_once '../MustSee/Data/Imatge.php';
require_once '../MustSee/Data/Lloc.php';
require_once '../MustSee/Data/Usuari.php';

// Establim la capçalera
header("Content-type: text/html; charset=utf-8");

// Obtenim una instancia de la base de dades
$dbm = MustSee\Database\DatabaseManager::getInstance(parse_ini_file('../db_config.ini'));

echo '<h1>Inici de les proves</h1>';
echo '<h2>Categories</h2>';
$categories = $dbm->getCategories();
foreach ($categories as $categoria) {
    echo "{$categoria->getId()} - {$categoria->getDescripcio()} <br>";
}

echo '<h2>Llocs</h2>';
$llocs = $dbm->getLlocs();
foreach ($llocs as $lloc) {
    echo "{$lloc->getId()} - {$lloc->getNom()} ({$lloc->getLatitud()}, {$lloc->getLongitud()}). {$lloc->getDescripcio()}<br>";
}

echo '<h2>Detall del lloc 5</h2>';
$lloc = $dbm->getLloc(5);
echo "{$lloc->getId()} - {$lloc->getNom()} ({$lloc->getLatitud()}, {$lloc->getLongitud()}). {$lloc->getDescripcio()}<br>";

echo '<h2>Imatges del lloc 5</h2>';
$imatges = $dbm->getImatgesFromLloc(5);
foreach ($imatges as $imatge) {
    echo "{$imatge->getId()} - {$imatge->getTitol()} <br>";
    echo "<img src=\"{$imatge->getUrl()}\" /> <br>";
}

echo '<h2>Imatge 5</h2>';
$imatge = $dbm->getImatge(5);
echo "{$imatge->getId()} - {$imatge->getTitol()} <br>";
echo "<img src=\"{$imatge->getUrl()}\" /> <br>";


echo "<h2>Comentaris del lloc 1</h2>";
$comentaris = $dbm->getComentarisFromLloc(1);
foreach ($comentaris as $comentari) {
    echo "{$comentari->getId()} - {$comentari->getUsuariId()} : {$comentari->getText()} <br>";
}

echo "<h2>Comentaris del usuari 1</h2>";
$comentaris = $dbm->getComentarisFromUsuari(1);
foreach ($comentaris as $comentari) {
    echo "{$comentari->getId()} - {$comentari->getUsuariId()} : {$comentari->getText()} <br>";
}

echo "<h2>Dades del usuari 1</h2>";
$usuari = $dbm->getUsuariById(1);
echo "{$usuari->getId()} - {$usuari->getNom()} : {$usuari->getCorreu()} <br>";

echo "<h2>Dades del usuari correu1@correu.com </h2>";
$usuari = $dbm->getUsuariByCorreu('correu1@correu.com ');
echo "{$usuari->getId()} - {$usuari->getNom()} : {$usuari->getCorreu()} <br>";


echo "<h2>Comprovar Contrasenya</h2>";
echo '<p>ha de ser correcte i es: ';
if ($dbm->comprovarContrasenya('testxavi@hotmail.com', '123456') === true) { // Correcta
    echo "<b>CORRECTE</b></p>";
} else {
    echo "<b>INCORRECTE</b><p>";
}

echo '<p>ha de ser incorrecte i es: ';
if ($dbm->comprovarContrasenya('testxavi@hotmail.com', '111111') === true) { // Incorrecta
    echo "<b>CORRECTE</b></p>";
} else {
    echo "<b>INCORRECTE</b><p>";
}

echo '<p>ha de ser incorrecte i es: ';
if ($dbm->comprovarContrasenya('testxavi@hotmail.es', '123456') === true) { // Incorrecta
    echo "<b>CORRECTE</b></p>";
} else {
    echo "<b>INCORRECTE</b><p>";
}
