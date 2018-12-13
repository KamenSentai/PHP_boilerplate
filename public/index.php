<?php

namespace Portfolio;

use Portfolio\Autoloader;

use Portfolio\Settings    as PS;
use Portfolio\Routes      as PR;
use Portfolio\Helpers     as PH;
use Portfolio\Models      as PM;
use Portfolio\Views       as PV;
use Portfolio\Controllers as PC;

require '../includes/Autoloader.php';
Autoloader::register();

$errors      = new PS\Errors();
$config      = new PS\Config();
$router      = new PR\Router(isset($_GET['url']) ? $_GET['url'] : '', __NAMESPACE__);
$inheritance = new PH\Inheritance();
$database    = new PM\Database(DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS);
$template    = new PV\Template($router, $inheritance, '/pages');

$router->get('/', function() use ($router, $template) {
  $data = [
    'title' => 'Alain Cao Van Truong | Full stack developer',
    'h1' => 'Portfolio',
  ];
  $template->render('home', $data);
}, 'home');

$router->get('/about', function() use ($router, $template) {
  $data = [
    'title' => 'About | Alain Cao Van Truong',
    'h1' => 'About',
  ];
  $template->render('about', $data);
}, 'about');

$router->get('/contact', function() use ($router, $template) {
  $data = [
    'title' => 'Contact | Alain Cao Van Truong',
    'h1' => 'Contact',
  ];
  $template->render('contact', $data);
}, 'contact');

$router->get('/works', function() use ($router, $template) {
  $data = [
    'title' => 'Contact | Alain Cao Van Truong',
    'h1' => 'Works',
  ];
  $template->render('works', $data);
}, 'works');

$router
  ->get(
    '/works/:slug',
    function($slug) use ($router, $template, $database) {
      $work = $database->fetch("SELECT * FROM works WHERE slug = '$slug'");

      $data = [
        'title' => "$work->title | Alain Cao Van Truong",
        'h1' => "$work->title",
      ];
      $template->render('work', $data);
    },
    'work'
  )
  ->with('slug', '[a-z0-9]+(-[a-z0-9]+)*')
;

$router->run();
