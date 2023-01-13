<?php
class Router
{
    private $controller;
    private $method;

    public function __construct()
    {
        $this->matchRoute();
    }

    public function matchRoute()
    {
        // var_dump(URL);
        $url = explode('/', URL);
        // var_dump($url);

        $this->controller = !empty($url[1]) ? $url[1] : 'User';

        if ($url[2] == "1" || $url[2] == "2") {
            $metodo = 'index';
        } else {
            $metodo = $url[2];
        }
        $this->method = !empty($url[2]) ? $metodo : 'defecto';

        $this->controller = $this->controller . 'Controller';

        require_once(__DIR__ . '/Controlador/' . $this->controller . '.php');
    }

    public function run()
    {
        $controller = new $this->controller();
        $method = $this->method;
        $controller->$method();
    }
}