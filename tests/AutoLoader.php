<?php
/**
 * Script que permet carregar les classes necessaries automàticament pels tests de PHPUnit
 *
 * @author Xavier García
 */
spl_autoload_register(function ($class) {
    require_once "..\\$class.php";
});