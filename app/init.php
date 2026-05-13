<?php
// Start session
session_start();

// Load Config
require_once 'config/config.php';

// Load Helpers
require_once 'helpers/session_helper.php';

// Autoload Core Libraries
spl_autoload_register(function($className){
    require_once 'core/' . $className . '.php';
});
