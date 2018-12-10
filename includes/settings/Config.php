<?php

namespace Portfolio\Settings;

class Config {
  public function __construct() {
    define('INDEX_PATH', $_SERVER['PHP_SELF']);
    define('INDEX_FILE', 'index.php');
    define('BASE_URL',   str_replace(INDEX_FILE, '', INDEX_PATH));
    define('DB_HOST', 'localhost');
    define('DB_PORT', '8889');
    define('DB_NAME', 'router');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
  }
}
