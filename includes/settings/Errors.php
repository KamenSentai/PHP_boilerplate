<?php

namespace Portfolio\Settings;

class Errors {
  public function __construct() {
    ini_set("display_startup_errors", 1);
    ini_set("display_errors", 1);
    error_reporting(E_ALL);
  }
}
