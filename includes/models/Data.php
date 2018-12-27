<?php

namespace Portfolio\Models;

class Data {
  private $base;

  public function __construct($base) {
    $this->base = $base;
  }

  public function getFile($file) {
    $path = "$this->base$file.json";

    if (file_exists($path)) {
      return json_decode(file_get_contents($path));
    } else {
      http_response_code(404);
    }
  }

  public function findElement($data, $key, $value) {
    foreach ($data as $element) {
      if ($element->$key === $value) {
        return $element;
        break;
      }
    }

    http_response_code(404);
  }
}
