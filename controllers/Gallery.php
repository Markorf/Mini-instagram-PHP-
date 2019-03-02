<?php

class Gallery extends Controller {

    protected $userModel;
    protected $pictureModel;

    function __construct() {
        if (!is_auth()) {
            header("Location: " . URLROOT . "/guests/login");
        }
        $this->pictureModel = $this->getModel("Picture");
        $this->userModel = $this->getModel("User");
    }

    function index() {
        $data = [
            "approot" => APPROOT,
            "urlroot" => URLROOT,
            "sitename" => SITENAME,
            "message" => "",
            "images" => [],
            "userID" => Session::getSession("userID"),
            "username" => $_SESSION["username"]
        ];
        $images = $this->pictureModel->getAllPicturesInfo();
        if (count($images) == 0) { // ako nema slika
            $data["message"] = 'Be first uploader on our website :)';
        } else {
            $data["images"] = $images; // stavi sve info u images (i naziv autora)
        }
        $this->getView("gallery/main.html", $data);
    }

    function contact() {
        $data = [
            "userID" => Session::getSession("userID"),
            "sitename" => SITENAME,
            "urlroot" => URLROOT,
            "approot" => APPROOT,
            "username" => $_SESSION["username"],
            "message" => ""
        ];
        $mail = new PHPMailer\PHPMailer\PHPMailer;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (($email = filter_input(INPUT_POST, "email")) && ($message = filter_input(INPUT_POST, "message")) && ($name = filter_input(INPUT_POST, "name")) && ($title = filter_input(INPUT_POST, "title"))) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $data["message"] = "You entered wrong email address. Please try again";
                }
            } else {
                $data["message"] = "You entered wrong information. Please try again";
            }
            // kraj za proveru (ako je sve u redu nastavi dalje)

            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';

            $mail->Host = "smtp.gmail.com";
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';

            $mail->Username = MAILADDRESS;
            $mail->Password = ADMINPW;

            $mail->setFrom(MAILADDRESS, "$name");

            $mail->addReplyTo(ADMINADDRESS, "Admin");
            $mail->addAddress(ADMINADDRESS, "Admin");

            $mail->IsHTML(true);
            $mail->Subject = "$title";
            $mail->Body = "<strong>from</strong> {$email}: <br> <strong>Title: $title</strong> <br> <strong>Text:</strong> <br> " . $message;

            if (!$mail->send()) {
                $data["message"] = "There is error: " . $mail->ErrorInfo;
                $data["message"] .= "<br>You have probably entered wrong password or your email is blocking php mailer";
            }
            $data["message"] = "MESSAGE SENDED";
        }

        $this->getView("gallery/contact.html", $data);
    }

    function logout() {
        Session::remove();
        $data = [
            "sitename" => SITENAME,
            "urlroot" => URLROOT,
            "approot" => APPROOT,
        ];
        header("Refresh:1; url=" . URLROOT . "/guests/login");
        $this->getView("gallery/logout.html", $data);
    }

    function upload() {
        $data = [
            "userID" => Session::getSession("userID"),
            "approot" => APPROOT,
            "urlroot" => URLROOT,
            "sitename" => SITENAME,
            "message" => "",
            "username" => $_SESSION["username"]
        ];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $title = filter_var($_POST["title"], FILTER_SANITIZE_STRING);
            $file = $_FILES["img-file"];
            // naziv fajla bez extenzije
            if (strlen($title) < 3) {
                $data["message"] = "Your image must have at least 3 char length";
            }
            if ($file["error"] > 0) {
                $data["message"] = "Error occured, image not loaded correctly";
            }
            $file["name"] = filter_var($file["name"], FILTER_SANITIZE_STRING);
            $check = getimagesize($file["tmp_name"]);
            if ($check == false) {
                $data["message"] = "Provide another image for upload!";
            }
            if (!empty($data["message"])) {
                return $this->getView("gallery/upload.html", $data);
            }
            // sve ok
            $tmpName = $file["tmp_name"];
            $ext = pathinfo($file["name"], PATHINFO_EXTENSION); // jpg, png, ...
            //  $destination_path = getcwd().DIRECTORY_SEPARATOR;
            // $destination_path jer "D:\xampp\pa sve do public foldera
            // (DIREC_SEP) je u stvari '/'
         
            $newImgName = uniqid(rand(1, 100)).".{$ext}";
            $dest = "../public/assets/images/".$newImgName; // mora ../ pa dalje ?

            if (!move_uploaded_file($tmpName, $dest)) {
                $data["message"] = "Error in uploading image";
                return $this->getView("gallery/upload.html", $data);
            } else {
                // sve ok
                // uzmi korisnika radi slanja njegovog id-a u pic model
                $userId = $this->userModel->getUserId($_SESSION["username"])->id;
                if (!$userId) {
                    $data["message"] = "Something went wrong";
                } else {
                    // ako je nasao korisnika uploaduj sliku
                    $this->pictureModel->updateImage($title, $dest, $userId);
                    $this->userModel->setPictureNum($userId);
                    $data["message"] = "File is uploaded successfully!";
                }
            }
        }
        $this->getView("gallery/upload.html", $data);
    }

    function full_gallery($isActive = false, $imgID = null) {
        $data = [
            "userID" => Session::getSession("userID"),
            "approot" => APPROOT,
            "urlroot" => URLROOT,
            "sitename" => SITENAME,
            "message" => "",
            "username" => $_SESSION["username"],
            "picturesLiked" => [], // niz id-eva slika koje je ulogovani korisnik vec lajkovao
            "images" => []
        ];
        $images = $this->pictureModel->getAllPicturesInfo();
        if (count($images) == 0) { // ako nema slika
            $data["message"] = 'EMPTY';
        } else {
            $data["images"] = $images; // stavi sve info u images (i naziv autora)
            $picturesLikedArr = $this->pictureModel->isUserAlreadyLiked(Session::getSession("username"));
            if (count($picturesLikedArr) > 0) {
                foreach ($picturesLikedArr as $idOfLikedPic) {
                    $data["picturesLiked"][] = $idOfLikedPic->id;
                }
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $validateIsActive = ($isActive == "false" || $isActive == "true");
            $validateImgID = ctype_digit($imgID);
            // validacija za xss
            if ($validateIsActive && $validateImgID) {
                // sve ok
                $currentPic = $this->pictureModel->getPictureById($imgID);
                if (!$currentPic) {
                    $data["message"] = "Cannot find this picture";
                    return $this->getView("gallery/full_gallery.html", $data);
                }
                // sve ok - izmeni sliku
                $userLiked = Session::getSession("username");
                // $uL -> trenutno ulogovani korisnik
                $picEdit = $this->pictureModel->editPictureStatus($imgID, $isActive, $userLiked);
                if (!$picEdit) {
                    die("Something went wrong");
                }
                return; // moze i die ili exit
            } else {
                die("Prevented xss attack!");
            }
        }
        $this->getView("gallery/full_gallery.html", $data);
    }

    function imgInfo(string $imgId) {
        $data = [
            "userID" => Session::getSession("userID"),
            "approot" => APPROOT,
            "urlroot" => URLROOT,
            "sitename" => SITENAME,
            "message" => "",
            "username" => $_SESSION["username"],
            "usersLikedArr" => [],
            "image" => [],
            "authorID" => ""
        ];
        $usersLikedIDs = [];
        $image = $this->pictureModel->getSinglePictureInfo($imgId);
        if (!$image) { // ako nema slike
            $data["message"] = 'This picture does not exist!';
        } else {
            $data["image"] = $image; // stavi sve info u images (i naziv autora)
            $data["message"] = "Here is picture info:";
            $data["authorID"] = $image->author_id;
            $usersLikedNames = ltrim($data["image"]->users_liked, SQLDELIMITER);
            $usersLikedNames = explode(SQLDELIMITER, $usersLikedNames); // niz sa username-ovima
            foreach($usersLikedNames as $username) {
                $userID = $this->userModel->getUserID($username);
                $userID->username = $username; // dodaj username da bi se lakse snasao u view-u
                $data["usersLikedArr"][] = $userID;
            }
               // ovo je niz objekata sacinjenih samo od imena i id-ova svih korisnika koji su lajkovali datu sliku            
        }
        $this->getView("gallery/img_info.html", $data);
    }

    function profile($idForViewUser = null) {
        $data = [
            "userID" => Session::getSession("userID"),
            "idForViewUser" => $idForViewUser,
            "approot" => APPROOT,
            "urlroot" => URLROOT,
            "sitename" => SITENAME,
            "message" => "",
            "username" => $_SESSION["username"],
            "user" => [],
            "userPic" => []
        ];
        if ($idForViewUser == null) {
            // ako je korisnik uneo url bez broja (onda udji na njegovu stranicu)
            $idForViewUser = Session::getSession("userID");
            $this->profile($idForViewUser);
        }
        // $idForViewUser se dobija iz url-a, dok userID je id trenutnog korisnika
        $user = $this->userModel->getUserByID($idForViewUser);
        
        if (!$user) {
            // moze gledati samo profile koji postoje
            die("User not found!");
        }
        $data["user"] = $user;
        $userPic = $this->pictureModel->getUserPictures($user->id);
        if (empty($userPic) || count($userPic) == 0) {
            $data["message"] = "There are no any pictures uploaded yet";
        } else {
            // ako ima slika ubaci u $data
            $data["userPic"] = $userPic;
        }
        $this->getView("gallery/profile.html", $data);
    }

    function deleteImage($imgID = null) {
        $data = [
            "userID" => Session::getSession("userID"),
            "approot" => APPROOT,
            "urlroot" => URLROOT,
            "sitename" => SITENAME,
            "message" => "",
            "username" => $_SESSION["username"],
            "image" => [],
        ];
        if (!$imgID) {
            header("Location: " . URLROOT);
        }

        $picture = $this->pictureModel->getPictureById($imgID);
        if (!$picture) {
            die("Something went wrong");
        }
        $data["image"] = $picture;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!$this->pictureModel->deletePicture($imgID)) {
                die("Something went wrong");
            }
            $data["message"] = "Picture deleted";
            // update broj slike nakon brisanja
            $user = $this->userModel->getUserID(Session::getSession("username"));
            $this->userModel->setPictureNum($user->id);
            $this->getView("gallery/deleteImage.html", $data);
            $userID = Session::getSession("userID");
            header("Refresh:1; url=" . URLROOT . "/gallery/profile/$userID");
        } else { // ako je get onda samo prikazi formu za brisanje
            $this->getView("gallery/deleteImage.html", $data);
        }
    }

    function editImage($imgID = null) {
        $data = [
            "userID" => Session::getSession("userID"),
            "approot" => APPROOT,
            "urlroot" => URLROOT,
            "sitename" => SITENAME,
            "message" => "",
            "username" => $_SESSION["username"],
            "image" => [],
        ];

        if (!$imgID) {
            header("Location: " . URLROOT);
        }
        $picture = $this->pictureModel->getPictureById($imgID);
        if (!$picture) {
            die("Something went wrong");
        }
        $data["image"] = $picture;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // postavi nove vrednosti i vrati ih nazad u view pa onda refresuj
            $data["image"]->name = addslashes($_POST["img_name"]);
            $data["image"]->posted_at = addslashes($_POST["img_date"]);
            $data["image"]->img_src = $_POST["img_src"]; // addslashes nisam stavio jer ce ih staviti mnogo pa nece naci src
            if (!$this->pictureModel->editPictureInfo($imgID, $data["image"]->name, $data["image"]->posted_at, $data["image"]->img_src)) {
                die("Something went wrong");
            }
            $data["message"] = "Picture changed";
            $this->getView("gallery/editImage.html", $data);
            $userID = Session::getSession("userID");
            header("Refresh:1; url=" . URLROOT . "/gallery/profile/$userID");
        } else {
            $this->getView("gallery/editImage.html", $data);
        }
    }

}
