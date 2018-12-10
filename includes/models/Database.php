<?php

namespace Portfolio\Models;

class Database {
  private $pdo;

  public function __construct($host, $port, $name, $user, $pass) {
    try {
      $this->pdo = new \PDO("mysql:host=$host;dbname=$name;port=$port", $user, $pass);
      $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    } catch (\Exception $e) {
      die('Could not connect');
    }
  }

  private function query($query) {
    return $this->pdo->query($query);
  }

  private function response($query) {
    if ($query) {
      return $query;
    } else {
      http_response_code(404);
    }
  }

  public function fetch($query) {
    return $this->response($this->query($query)->fetch());
  }

  public function fetchAll($query) {
    return $this->response($this->query($query)->fetchAll());
  }
}
