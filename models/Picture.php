<?php

class Picture {

    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    function getAllPictures() {
        $sql = "SELECT * FROM pictures";
        $this->db->query($sql);
        return $this->db->getAllRows();
    }

    function getCC() {
        $sql = "SELECT * FROM pictures";
        $this->db->query($sql);
        return $this->db->columnCount();
    }

    function updateImage(string $imgName, string $imgSrc, string $authorId) {
        $sql = "INSERT INTO pictures (name, img_src, author_id) VALUES (:imgName, :imgSrc, :authorId)";
        settype($authorId, "int");
        $this->db->query($sql);
        $this->db->bind(":imgName", $imgName);
        $this->db->bind(":imgSrc", $imgSrc);
        $this->db->bind(":authorId", $authorId);
        return $this->db->execute();
    }

    function getAllPicturesInfo() {
        $sql = "SELECT pictures.author_id, pictures.id, pictures.name, pictures.img_src, pictures.number_of_likes, pictures.posted_at, users.username 
                FROM pictures
                INNER join users
                ON pictures.author_id = users.id
            ";
        $this->db->query($sql);
        return $this->db->getAllRows();
    }

    function getSinglePictureInfo(string $picID) {
        $sql = "SELECT pictures.author_id, pictures.id, pictures.name, pictures.img_src, pictures.number_of_likes, pictures.users_liked , pictures.posted_at, users.username 
                FROM pictures
                INNER join users
                ON pictures.author_id = users.id
                WHERE pictures.id = :picID
            ";
        $this->db->query($sql);
        $this->db->bind(":picID", $picID);
        return $this->db->getSingleRow();
    }

    function getUserPictures(string $userID) {
        $sql = "SELECT * FROM pictures WHERE author_id = :userID";
        $this->db->query($sql);
        $this->db->bind(":userID", $userID);
        return $this->db->getAllRows();
    }

    function getPictureById(string $picID) {
        $sql = "SELECT * FROM pictures WHERE pictures.id = :picID";
        $this->db->query($sql);
        $this->db->bind(":picID", $picID);
        return $this->db->getSingleRow();
    }

    function deletePicture(string $picID) {
        $sql = "DELETE FROM pictures WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(":id", $picID);
        return $this->db->execute();
    }

    function editPictureInfo(string $picID, string $name, string $date, string $src) {
        $sql = "UPDATE pictures SET name = :name, posted_at = :date, img_src = :img_src WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(":id", $picID);
        $this->db->bind(":name", $name);
        $this->db->bind(":date", $date);
        $this->db->bind(":img_src", $src);
        return $this->db->execute();
    }

    function editPictureStatus(string $picID, string $isLiked, string $userLiked) {
        // $isLiked ($isActive) je u stvari string "true" ili "false"
        $userLiked = SQLDELIMITER . "$userLiked"; // stavi <|> da bi mogao kasnije da idem explode (mora u navodnike i nece da radi bind ako stavim <> vrv
        if ($isLiked === "true") {
            $sql = "UPDATE pictures SET number_of_likes = number_of_likes + 1, users_liked=concat(users_liked, '$userLiked') WHERE id = :id";
        } else {
            $sql = "UPDATE pictures SET number_of_likes = number_of_likes - 1, users_liked = REPLACE(users_liked, '$userLiked', '') WHERE id = :id";
        }
        $this->db->query($sql);
        $this->db->bind(":id", $picID);
//        $this->db->bind(":userLiked", SQLDELIMITER.$userLiked);  // ne radi vrv zbog delimitera
        return $this->db->execute();
    }
    // vraca niz svih slika koje je trenutno ulogovani korisnik lajkovao (niz id-ova)
    function isUserAlreadyLiked(string $username) {
        $search = SQLDELIMITER.$username;
        $sql = "SELECT id FROM `pictures` WHERE users_liked LIKE '%$search%'";
        $this->db->query($sql);
        return $this->db->getAllRows();
    }

}
