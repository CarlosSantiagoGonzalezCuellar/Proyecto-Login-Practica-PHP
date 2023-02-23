<?php

require_once "./Modelo/userModelo.php";
require_once "./Controlador/respuestas.php";
require_once "./Controlador/userValidate.php";

$_respuestas = new respuestas;
class UserController
{

    //<!-- ========== METODO DE URL INDEX DE USER: 1=GET - 2=PATCH ========== -->
    public function index()
    {
        $_usuarios = new usuarios;
        $_userVali = new userValidate;
        $_respuestas = new respuestas;
        $url = explode('/', URL);

        //<!-- ========== METODO GET SI URL = 1 ========== -->
        if ($url[2] == "1") {
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                if (isset($_GET["id"])) {
                    $usuarioId = $_GET["id"];
                    $datosUsuario = $_usuarios->obtenerUsuario($usuarioId);
                    header("Content-Type: application/json");
                    echo json_encode($datosUsuario);
                    http_response_code(200);
                } else {
                    $listaUsuarios = $_usuarios->listaUsuarios();
                    header("Content-Type: application/json");
                    echo json_encode($listaUsuarios);
                    http_response_code(200);
                }
            } else {
                header("Content-Type: application/json");
                $datosArray = $_respuestas->error_405();
                echo json_encode($datosArray);
            }

            //<!-- ========== METODO PATCH SI URL = 2 ========== -->
        } elseif ($url[2] == "2") {
            if ($_SERVER["REQUEST_METHOD"] == "PATCH") {
                //Recibe datos enviados 
                $postBody = file_get_contents("php://input");
                //Envia datos al manejador
                $datosArray = $_userVali->patch($postBody);
                //Devuelve respuesta
                header("Content-Type: application/json");
                if (isset($datosArray["result"]["error_id"])) {
                    $responseCode = $datosArray["result"]["error_id"];
                    http_response_code($responseCode);
                } else {
                    http_response_code(200);
                }
                echo json_encode($datosArray);
            } else {
                header("Content-Type: application/json");
                $datosArray = $_respuestas->error_405();
                echo json_encode($datosArray);
            }
        } else {
            header("Content-Type: application/json");
            $datosArray = $_respuestas->error_405();
            echo json_encode($datosArray);
        }
    }

    //<!-- ========== METODO DE URL CREATE DE USER: POST ========== -->
    public function create()
    {
        $_userVali = new userValidate;
        $_respuestas = new respuestas;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //Recibe datos enviados 
            $postBody = file_get_contents("php://input");
            //Envia al manejador
            $datosArray = $_userVali->post($postBody);
            //Devuelve respuesta
            header("Content-Type: application/json");
            if (isset($datosArray["result"]["error_id"])) {
                $responseCode = $datosArray["result"]["error_id"];
                http_response_code($responseCode);
            } else {
                http_response_code(200);
            }
            echo json_encode($datosArray);
        } else {
            header("Content-Type: application/json");
            $datosArray = $_respuestas->error_405();
            echo json_encode($datosArray);
        }
    }

    //<!-- ========== METODO DE URL DELETE DE USER: POST ========== -->
    public function delete()
    {
        $_userVali = new userValidate;
        $_respuestas = new respuestas;
        if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
            //Recibe datos enviados 
            $postBody = file_get_contents("php://input");
            //Envia datos al manejador
            $datosArray = $_userVali->delete($postBody);
            //Devuelve respuesta
            header("Content-Type: application/json");
            if (isset($datosArray["result"]["error_id"])) {
                $responseCode = $datosArray["result"]["error_id"];
                http_response_code($responseCode);
            } else {
                http_response_code(200);
            }
            echo json_encode($datosArray);
        } else {
            header("Content-Type: application/json");
            $datosArray = $_respuestas->error_405();
            echo json_encode($datosArray);
        }
    }

    //<!-- ========== METODO POR DEFECTO DE USER: GET ========== -->
    public function defecto()
    {
        $_usuarios = new usuarios;
        $_respuestas = new respuestas;
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (isset($_GET["id"])) {
                $usuarioId = $_GET["id"];
                $datosUsuario = $_usuarios->obtenerUsuario($usuarioId);
                header("Content-Type: application/json");
                echo json_encode($datosUsuario);
                http_response_code(200);
            } else {
                $listaUsuarios = $_usuarios->listaUsuarios();
                header("Content-Type: application/json");
                echo json_encode($listaUsuarios);
                http_response_code(200);
            }
        } else {
            header("Content-Type: application/json");
            $datosArray = $_respuestas->error_405();
            echo json_encode($datosArray);
        }
    }
}
