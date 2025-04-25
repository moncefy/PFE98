<?php
class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $conn;

    public function __construct($host, $user, $pass, $dbname) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->connect();
    }

    private function connect() {
        try {
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            // Set charset to utf8mb4
            $this->conn->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die("Une erreur de connexion à la base de données est survenue. Veuillez réessayer plus tard.");
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        try {
            $result = $this->conn->query($sql);
            if ($result === false) {
                throw new Exception("Query failed: " . $this->conn->error);
            }
            return $result;
        } catch (Exception $e) {
            error_log("Database Query Error: " . $e->getMessage());
            return false;
        }
    }

    public function prepare($sql) {
        try {
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            return $stmt;
        } catch (Exception $e) {
            error_log("Database Prepare Error: " . $e->getMessage());
            return false;
        }
    }

    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    public function getLastError() {
        return $this->conn->error;
    }

    public function getLastId() {
        return $this->conn->insert_id;
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function __destruct() {
        $this->close();
    }
}
?> 