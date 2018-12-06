<?php

namespace Portfolio\Routes;

class Route {
  private $path;
  private $callable;
  private $namespace;
  private $matches = [];
  private $params  = [];

  public function __construct($path, $callable, $namespace) {
    $this->path      = trim($path, '/');
    $this->callable  = $callable;
    $this->namespace = $namespace;
  }

  public function with($param, $regex) {
    $this->params[$param] = str_replace('(', '(?:', $regex);
    return $this;
  }

  public function match($url) {
    $url   = trim($url, '/');
    $path  = preg_replace_callback('#:([\w]+)#', [$this, 'matching'], $this->path);
    $regex = "#^$path$#i";

    if (!preg_match($regex, $url, $matches)) {
      return false;
    }

    array_shift($matches);

    $this->matches = $matches;

    return true;
  }

  private function matching($match) {
    if (isset($this->params[$match[1]])) {
      return "({$this->params[$match[1]]})";
    }

    return '([^/]+)';
  }

  public function call() {
    if (is_string($this->callable)) {
      $params     = explode('#', $this->callable);
      $controller = $this->namespace . '\\Controllers\\' . $params[0];
      $controller = new $controller();
      return call_user_func_array([$controller, $params[1]], $this->matches);
    } else {
      return call_user_func_array($this->callable, $this->matches);
    }
  }

  public function path($params) {
    $path = $this->path;

    foreach ($params as $key => $value) {
      $path = str_replace(":$key", $value, $path);
    }

    return $path;
  }
}
