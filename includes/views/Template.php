<?php

namespace Portfolio\Views;

class Template {
  private $router;

  public function __construct($router) {
    $this->router = $router;
  }

  public function render($page, $data = []) {
    require __DIR__ . "/base.php";
  }
}
