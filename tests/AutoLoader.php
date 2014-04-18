<?php
/**
 * Permet carregar les classes necessaries automàticament pels tests
 */
spl_autoload_register(function ($class) {
    include '..\\' . $class . '.php';
});