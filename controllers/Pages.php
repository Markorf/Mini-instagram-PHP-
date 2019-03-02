<?php

class Pages extends Controller{
     function __construct() {
        if (is_auth()) {
            // ako je vec ulogovan, redirektuj ga
            header("Location: " .URLROOT."/gallery");
        }
    }

    function index() {
        $data = [
            "approot" => APPROOT,
            "urlroot" => URLROOT,
            "sitename" => SITENAME
        ];

        $this->getView("pages/main.html", $data);
    }

    function about() {
        $data = [
            "urlroot" => URLROOT,
            "approot" => APPROOT,
            "version" => VERSION,
            "sitename" => SITENAME,
            "author" => AUTHOR,
        ];
        $this->getView("pages/about.html", $data);
    }
}
