<?php

namespace Portfolio\Views;

class Template {
  private $router;
  private $inheritance;
  private $data;
  private $path;

  public function __construct($router, $inheritance, $path) {
    $this->router      = $router;
    $this->inheritance = $inheritance;
    $this->path        = $path;

    $router->template($this);
  }

  public function data($data) {
    $this->data = $data;
  }

  public function render($page, $data = []) {
    $inheritance = $this->inheritance;
    $path        = $this->path;

    $file = $this->file($page);
    $dir  = __DIR__ . '/';

    if (!file_exists($file)) {
      http_response_code(404);
      $page = '404';
    }

    require $this->file($page);
  }

  private function file($page) {
    return __DIR__ . "{$this->path}/{$page}.php";
  }
}
