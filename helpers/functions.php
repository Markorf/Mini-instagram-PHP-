<?php

function h(string $str) {
    return htmlspecialchars($str, ENT_QUOTES);
}

function is_auth() {
    if (isset($_SESSION["auth_key"]) && is_string($_SESSION["auth_key"])) {
        return true;
    } else {
        return false;
    }
}