<?php

/**
 * Description of Database
 *
 * @author EC
 */
class Database {

    private $dbname = DB;
    private $username = USERNAME;
    private $password = PASSWORD;
    private $servername = SERVERNAME;
    
    private $conn;
    private $stmt;
    private $error;

    function __construct() {
        try {
            $dsn = "mysql:host=$this->servername;dbname=$this->dbname";
            $options = [
                 PDO::ATTR_PERSISTENT => true,
                 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $ex) {
            $this->error = $ex->getMessage();
            echo "Error is: $this->error";
        }
    }
    function query(string $sql) {
        $this->stmt = $this->conn->prepare($sql);
    }
    function execute() {
        return $this->stmt->execute() or die("Something went wrong");
    }
    function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type); 
    }
    function getAllRows() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
     function getSingleRow() {
        $this->execute();
        return $this->stmt->fetch();
    }
    function rowCount() {
        $this->execute();
        return $this->stmt->rowCount();
    }
    function columnCount() {
        $this->execute();
        return $this->stmt->columnCount();
    }
    function lastId() {
        return $this->conn->lastInsertId();
    }

}
