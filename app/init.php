<?php
require_once 'config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload Core Libraries
spl_autoload_register(function($className){
    require_once 'core/' . $className . '.php';
});
