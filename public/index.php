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
    'title' => 'Hello',
    'h1' => 'Hello world',
  ];
  $template->render('home', $data);
}, 'home.show');

$router->get('/posts', function() use ($router, $template) {
  $data = [
    'title' => 'Posts',
    'h1' => 'Hello world',
  ];
  for ($i=0; $i < 10 ; $i++) {
    echo '<br>';
    echo "<a href=\"{$router->url('post.show', ['id' => $i])}\">Post $i</a>";
  }
  $template->render('posts', $data);
}, 'posts.show');

$router->get('/articles/:id', 'ArticlesController#show');

$router
  ->get(
    '/posts/:id',
    function($id) use ($router, $template, $database) {
      $work = $database->fetch("SELECT * FROM works WHERE id=$id");

      echo '<pre>';
      var_dump($work);
      echo '</pre>';

      echo '<br>';

      $works = $database->fetchAll("SELECT * FROM works WHERE id=$id");

      echo '<pre>';
      var_dump($works);
      echo '</pre>';

      $data = [
        'title' => $id,
        'h1' => "Post $id",
      ];
      $template->render('posts', $data);
    },
    'post.show'
  )
  ->with('id', '[0-9]+')
;

$router->run();
