<?php

/**
 * Description of Controller
 *
 * @author EC
 */
abstract class Controller {

    function getModel(string $model) {
        $filePath = "../app/models/${model}.php";

        if (file_exists($filePath)) {
            require_once $filePath;
            return new $model;
        } else {
            exit("Model $model does not exist!");
        }
    }

    function getView(string $view, $data = [
        "approot" => APPROOT,
        "urlroot" => URLROOT,
        "sitename" => SITENAME,
        "message" => "",
    ]) {
        $filePath = "../app/views/${view}";
        if (file_exists($filePath)) {
            $loader = new \Twig_Loader_Filesystem('../app/views');
            $twig = new \Twig_Environment($loader);
            echo $twig->render($view, $data);
        } else {
            exit("View $view does not exist!");
        }
    }

}
