<?php

namespace Portfolio\Models;

class Database {
  private $host;
  private $port;
  private $name;
  private $user;
  private $pass;
  private $pdo;

  public function __construct($host, $port, $name, $user, $pass) {
    $this->host = $host;
    $this->port = $port;
    $this->name = $name;
    $this->user = $user;
    $this->pass = $pass;
  }

  private function connect() {
    $url = "mysql:host=$this->host;dbname=$this->name;port=$this->port";

    try {
      $this->pdo = new \PDO($url, $this->user, $this->pass);
      $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    } catch (\Exception $e) {
      die('Could not connect to database');
    }
  }

  private function query($statement) {
    if (is_null($this->pdo)) {
      $this->connect();
    }

    return $this->pdo->query($statement);
  }

  private function response($statement) {
    if ($statement) {
      return $statement;
    } else {
      http_response_code(404);
    }
  }

  public function fetch($statement) {
    return $this->response($this->query($statement)->fetch());
  }

  public function fetchAll($statement) {
    return $this->response($this->query($statement)->fetchAll());
  }
}
