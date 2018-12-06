<?php

namespace Portfolio\Views;

class Template {
  private $router;
  private $inheritance;
  private $path;
  private $error;

  public function __construct($router, $inheritance, $path, $error) {
    $this->router      = $router;
    $this->inheritance = $inheritance;
    $this->path        = $path;
    $this->error       = $error;
  }

  public function render($page, $data = []) {
    $inheritance = $this->inheritance;
    $path        = $this->path;

    $file = $this->file($page);
    $dir  = __DIR__ . '/';

    if (!file_exists($file)) {
      $page = $this->error;
      $data = $this->error();
    }

    require $this->file($page);
  }

  private function file($page) {
    return __DIR__ . "{$this->path}/{$page}.php";
  }

  private function error() {
    $data = [
      'title' => '404',
    ];

    return $data;
  }
}
