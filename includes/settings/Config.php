<?php

namespace Portfolio\Settings;

class Config {
  public function __construct() {
    define('INDEX', 'index.php');
    define('PATH', str_replace(INDEX, '', $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']));
    define('BASE', str_replace(getenv('HTTP_HOST') . '/', '', PATH));
    define('DB_HOST', 'localhost');
    define('DB_PORT', '8889');
    define('DB_NAME', 'router');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
  }
}
