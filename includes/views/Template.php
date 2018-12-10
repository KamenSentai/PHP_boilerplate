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
    $router      = $this->router;
    $inheritance = $this->inheritance;
    $path        = $this->path;

    $file = $this->file($page);
    $dir  = __DIR__ . '/';

    if (!file_exists($file) || $page == '404') {
      http_response_code(404);
    }

    if (http_response_code() === 404) {
      require $this->file('404');
    } else {
      require $this->file($page);
    }
  }

  private function file($page) {
    return __DIR__ . "{$this->path}/{$page}.php";
  }
}
