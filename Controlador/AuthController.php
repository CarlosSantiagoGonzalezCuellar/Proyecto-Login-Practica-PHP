<?php

require_once "./Modelo/authModelo.php";
require_once "./Controlador/authValidate.php";
require_once "./Controlador/respuestas.php";

class AuthController
{
    //<!-- ========== METODO POR DEFECTO DE AUTH: POST ========== -->
    public function defecto()
    {
        $_authVali = new authVali;
        $_respuestas = new respuestas;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $headers = getallheaders();
            if (isset($headers["correo"]) && isset($headers["password"])) {
                //Recibe datos enviados 
                $send = [
                    "correo" => $headers["correo"],
                    "password" => $headers["password"]
                ];
                $postBody = json_encode($send);
            } else {
                //Recibe datos enviados 
                $postBody = file_get_contents("php://input");
            }
            
            //Envia datos al manejador
            $datosArray = $_authVali->login($postBody);
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
}
