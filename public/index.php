<?php

namespace Portfolio;

include '../includes/settings/errors.php';

use Portfolio\Autoloader;

use Portfolio\Settings    as PS;
use Portfolio\Routes      as PR;
use Portfolio\Models      as PM;
use Portfolio\Views       as PV;
use Portfolio\Controllers as PC;

require '../includes/Autoloader.php';
Autoloader::register();

$errors = new PS\Errors();
$config = new PS\Config();

$url = isset($_GET['url']) ? $_GET['url'] : '';

$router = new PR\Router($url, __NAMESPACE__);

$router->get('/', function() use ($router) {

});

$router->run();
