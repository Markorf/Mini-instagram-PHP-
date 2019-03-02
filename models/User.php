<?php

class User {
    protected $db;
    function __construct() {
        $this->db = new Database();
    }

    function insertUser(string $username, string $email, string $password) {
        $authKey = md5(uniqid(rand()));
        $sql = "INSERT INTO users (username, email, password, auth_key) VALUES(:username, :email, :password, :authKey)";
        $this->db->query($sql);
        $this->db->bind(":username", $username);
        $this->db->bind(":password", $password);
        $this->db->bind(":email", $email);
        $this->db->bind(":authKey", $authKey);
        return $this->db->execute();
    }
    function checkUserByEmail(string $email) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $this->db->query($sql);
        $this->db->bind(":email", $email);
        
        return $this->db->rowCount() > 0;
    }
    
     function checkUserByUsername(string $username) {
        $sql = "SELECT id FROM users WHERE username = :username";
        $this->db->query($sql);
        $this->db->bind(":username", $username);
        
        return $this->db->rowCount() > 0;
    }
    function getUserByEmail(string $email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $this->db->query($sql);
        $this->db->bind(":email", $email);
        
        if ($this->db->rowCount() == 0) {
            return false;
        }
        return $this->db->getSingleRow();
    }
    
     function getUserByUsername(string $username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $this->db->query($sql);
        $this->db->bind(":username", $username);
        
        if ($this->db->rowCount() == 0) {
            return false;
        }
        return $this->db->getSingleRow();
    }
    
     function getUserByID(string $userID) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(":id", $userID);
        
        if ($this->db->rowCount() == 0) {
            return false;
        }
        return $this->db->getSingleRow();
    }
    
    function getUserID(string $username) {
        $sql = "SELECT id FROM users WHERE username = :username";
        $this->db->query($sql);
        $this->db->bind(":username", $username);
        
        if ($this->db->rowCount() == 0) {
            return false;
        }
        return $this->db->getSingleRow();
    }
   function setPictureNum(string $authorID) {
        $sql = "UPDATE users SET picture_num = (SELECT count(id) FROM pictures WHERE author_id = '$authorID') WHERE id = '$authorID'";
        $this->db->query($sql);
        return $this->db->execute();
        
   }
}
