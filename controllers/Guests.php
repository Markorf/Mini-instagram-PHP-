<?php

class Guests extends Controller {

    protected $userModel;
    protected $pictureModel;

    function __construct() {
        if (is_auth()) {
            // ako je vec ulogovan, redirektuj ga
            header("Location: " .URLROOT."/gallery"); 
        }
        $this->pictureModel = $this->getModel("Picture");
        $this->userModel = $this->getModel("User");
    }


    function register() {
        $data = [
            "sitename" => SITENAME,
            "urlroot" => URLROOT,
            "approot" => APPROOT,
            "passwordErr" => "",
            "usernameErr" => "",
            "emailErr" => "",
            "username" => "",
            "password" => "",
            "email" => "",
            "passwordConfirm" => "",
            "message" => "",
            "captchaErr" => ""
        ];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data["username"] = filter_var($_POST["register_username"], FILTER_SANITIZE_STRING);
            $data["email"] = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
            $data["password"] = filter_var($_POST["register_password"], FILTER_SANITIZE_STRING);
            $data["passwordConfirm"] = filter_var($_POST["confirm-password"], FILTER_SANITIZE_STRING);

            // provera vrednosti
            if (empty($data["username"]) || strlen($data["username"]) < 5) {
                $data["usernameErr"] = "Username must have at least 5 chars";
            } elseif ($this->userModel->checkUserByUsername($data["username"])) {
                $data["usernameErr"] = "This username already exists";
            }
            if (empty($data["password"]) || strlen($data["password"]) < 5) {
                $data["passwordErr"] = "Password must have at least 5 chars";
            } elseif ($data["password"] !== $data["passwordConfirm"]) {
                $data["passwordErr"] = "Passwords did not match";
            }
            if (empty($data["email"]) || strlen($data["email"]) < 5) {
                $data["emailErr"] = "Your email must be at least 5 chars";
            } elseif ($this->userModel->checkUserByEmail($data["email"])) {
                $data["emailErr"] = "This email already exists";
            } else if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                $data["emailErr"] = "Please enter valid email";
            }
//             validacija google captcha
            $secretKey = "6LeaiGcUAAAAAIuiZqsl1yoJ3K35kTshjDInqwyG";
            $responseKey = $_POST["g-recaptcha-response"];
            $userIP = $_SERVER["REMOTE_ADDR"];
            $googleUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";

            $response = file_get_contents($googleUrl);
            $response = json_decode($response);
            if (!$response->success) {
                $data["captchaErr"] = "Not validated";
            }
            // provera da li je sve ok
            if (empty($data["passwordErr"]) && empty($data["usernameErr"]) && empty($data["emailErr"]) && empty($data["captchaErr"])) {
                // ako je sve u redu ubaci korisnika
                $data["message"] = "You are registered successfully!";
                $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
                $this->userModel->insertUser($data["username"], $data["email"], $data["password"]);
                header("Refresh:1; url=" . URLROOT . "/guests/login");
            }
        }

        $this->getView("guests/register.html", $data);
    }

    function login() {
        $data = [
            "sitename" => SITENAME,
            "urlroot" => URLROOT,
            "approot" => APPROOT,
            "message" => "",
        ];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data["email"] = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
            $data["password"] = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
            $user = $this->userModel->getUserByEmail($data["email"]);
            if (!$user) {
                // ako nema korisnika registrovanog
                $data["message"] = "This user is not registered";
            } else {
                // ako postoji korisnik
                // proveri passworde
                if (password_verify($data["password"], $user->password)) {
                    $data["message"] = "You are successfully logged in";
                    $this->enterSessions($user);
                    header("Refresh:1; url=" . URLROOT . "/gallery"); // paziti na (/) zbog putanje slike
                } else {
                    // nije dobar pw
                    $data["message"] = "Invalid password";
                }
            }
        }
        $this->getView("guests/login.html", $data);
    }

    function contact() {
        $data = [
            "sitename" => SITENAME,
            "urlroot" => URLROOT,
            "approot" => APPROOT,
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

        $this->getView("guests/contact.html", $data);
    }

    function enterSessions($user) {
        Session::setSession("username", $user->username);
        Session::setSession("auth_key", $user->auth_key);
        Session::setSession("userID", $user->id);
    }

}
