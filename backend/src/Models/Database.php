<?php
// src/Models/Database.php

namespace App\Models;
use RuntimeException;

class Database {

    public static  function getDatabaseConnection()
    {
        $servername = "localhost"; //scandi-db.cp6c6umsmx2q.eu-north-1.rds.amazonaws.com"
        $username = "root"; //admin
        $password = "1234"; //Shimaa6488
        $dbname = "scandi4ecommerce";

        $conn = new \mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new RuntimeException("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }

}