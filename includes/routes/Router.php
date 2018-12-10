<?php

namespace Portfolio\Routes;

class Router {
  private $url;
  private $namespace;
  private $template;
  private $routes = [];
  private $names  = [];

  public function __construct($url, $namespace) {
    $this->url       = $url;
    $this->namespace = $namespace;
  }

  public function template($template) {
    $this->template = $template;
  }

  private function add($path, $callable, $name, $method) {
    $route = new Route($path, $callable, $this->namespace);
    $this->routes[$method][] = $route;

    if (is_string($callable) && $name === null) {
      $name = $callable;
    }

    if ($name) {
      $this->names[$name] = $route;
    }

    return $route;
  }

  public function get($path, $callable, $name = null) {
    return $this->add($path, $callable, $name, 'GET');
  }

  public function post($path, $callable, $name = null) {
    return $this->add($path, $callable, $name, 'POST');
  }

  public function url($name, $params = []) {
    if (!isset($this->names[$name])) {
      throw new \Exception('Router: No route matches this name');
    }

    return BASE_URL . $this->names[$name]->path($params);
  }

  public function run() {
    if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
      throw new \Exception('Router: REQUEST_METHOD does not exists');
    }

    foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
      if ($route->match($this->url)) {
        return $route->call();
      }
    }

    return $this->template->render('404');
  }
}
