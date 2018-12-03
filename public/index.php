<?php

use Portfolio\Autoloader;

require '../includes/Autoloader.php';
Autoloader::register();

$router = new Portfolio\Routes\Router();
