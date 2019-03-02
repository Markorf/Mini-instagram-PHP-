<?php

/**
 * Description of Session
 *
 * @author EC
 */
class Session {

    static function start() {
        session_start();
    }

    static function setSession(string $sessionName, string $sessionValue) {
        $_SESSION["$sessionName"] = $sessionValue;
    }
    static function remove() {
        session_destroy();
    }
    static function unsetSession(string $sessionName) {
        unset($_SESSION["$sessionName"]);
    }
    static function getSession(string $sessionName) {
        return $_SESSION["$sessionName"];
    }
   static function display() {
        echo "<pre>
                ". print_r($_SESSION) ."
            </pre>";
    }

}
