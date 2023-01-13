<?php
class UserController
{
    public function index()
    {
        $url = explode('/', URL);
        if ($url[2] == "1") {
            echo 'USER: Metodo GET';
        }elseif($url[2] == "2") {
            echo 'USER: Metodo PATCH';
        }else{
            echo 'Metodo incorrecto...';
        }
        
    }
    public function create()
    {
        echo 'USER: Metodo POST';
    }
    public function delete()
    {
        echo 'USER: Metodo DELETE';
    }

    public function defecto()
    {
        echo 'USER: Metodo GET';
    }
}