<?php

namespace Portfolio;

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
  echo 'homepage' . '<br>';
  echo $router->url('post.show') . '<br>';
  echo $router->url('posts.show', ['id' =>  1, 'slug' => 'hello-world']);
}, 'index.show');

$router->get('/posts', function() {
  echo 'GET posts';
}, 'post.show');

$router
  ->get(
    '/posts/:id-:slug',
    function($id, $slug) use ($router) {
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
