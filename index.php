<?php
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/router.php');
require_once "./Modelo/authModelo.php";

$router = new Router();
$router->run();

$_auth = new auth;
$_auth->inactivarToken();

?>
