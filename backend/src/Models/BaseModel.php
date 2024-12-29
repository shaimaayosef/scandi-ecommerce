<?php

namespace App\Models;

use RuntimeException;

abstract class BaseModel {
    protected $conn;
    
    public function __construct() {
        $this->conn = Database::getDatabaseConnection();
    }
    
    abstract public function getGraphQLType();
    abstract public function resolve($root, $args = []);
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}