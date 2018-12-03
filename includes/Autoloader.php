<?php

namespace Portfolio;

class Autoloader {
  static function register() {
    spl_autoload_register(array(__CLASS__, 'autoload'));
  }

  static function autoload($instance) {
    $path = str_replace(__NAMESPACE__ . '\\', '', $instance);
    $class = str_replace('\\', '/', $path);

    if (file_exists("../includes/{$class}.php"))
      require "../includes/{$class}.php";
    else
      die("Class {$class} not found");
  }
}
