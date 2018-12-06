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
$template    = new PV\Template($router, $inheritance, '/pages', '404');

$router->get('/', function() use ($router, $template) {
  $data = [
    'title' => 'Hello',
    'h1' => 'Hello world',
  ];
  $template->render('index', $data);
}, 'index.show');

$router->get('/posts', function() use ($router, $template) {
  // echo $router->url('post.show') . '<br>';
  // echo $router->url('posts.show', ['id' =>  1, 'slug' => 'hello-world']);
  $data = [
    'title' => 'Posts',
  ];
  $template->render('posts', $data);
}, 'post.show');

$router
  ->get(
    '/posts/:id-:slug',
    function($id, $slug) use ($router, $template) {
      // echo "Article $slug : $id";
      echo $router->url('posts.show', ['id' =>  1, 'slug' => 'hello-world']) .'<br>';
      echo "<a href=\"{$router->url('post.show')}\">Post</a>";
      echo '<br>';
      echo "<a href=\"{$router->url('index.show')}\">Index</a>";
    },
    'posts.show'
  )
  ->with('id', '[0-9]+')
  ->with('slug', '[a-z\-0-9]+')
;

$router->get('/articles/:id', 'ArticlesController#show');

$router
  ->get(
    '/posts/:id',
    function($id) {
      echo 'GET posts ' . $id;
      ?>

      <form action="" method="post">
        <input type="text" name="name">
        <button type="submit">Envoyer</button>
      </form>

      <?php
    }
  )
  ->with('id', '[0-9]+')
;

$router->post('/posts/:id', function($id) {
  echo 'POST posts ' . $id . '<br>';
  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
});

$router->run();
