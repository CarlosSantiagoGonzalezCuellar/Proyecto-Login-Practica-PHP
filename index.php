<?php
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/router.php');
require_once "./Modelo/tokenModelo.php";

$router = new Router();
$router->run();

$_token = new token;
$_token->inactivarToken();

?>
