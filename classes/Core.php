<?php

/**
 * Description of Core
 *
 * @author EC
 */
class Core {

    protected $currentControler = "Pages";
    protected $currentMethod = "index";
    protected $passedArgs = [];

    function __construct() {
        $urlList = $this->getUrl();
        if (isset($urlList[0])) {
            $fileName = ucwords($urlList[0]);
            if (file_exists("../app/controllers/{$fileName}.php")) {
                $this->currentControler = $fileName;
                unset($urlList[0]);
            }
        }

        require_once "../app/controllers/{$this->currentControler}.php";
        $this->currentControler = new $this->currentControler;

        if (isset($urlList[1])) {
            if (method_exists($this->currentControler, $urlList[1])) {
                $this->currentMethod = $urlList[1];
                unset($urlList[1]);
            }
        }
        // poziv metode
        $this->passedArgs = $urlList ? array_values($urlList) : [];
        call_user_func_array([$this->currentControler, $this->currentMethod], $this->passedArgs);
    }

    function getUrl() {
        if ($url = filter_input(INPUT_GET, "url")) {
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = rtrim($url, "/");
            $url = explode("/", $url);
            return $url;
        }
    }

}
