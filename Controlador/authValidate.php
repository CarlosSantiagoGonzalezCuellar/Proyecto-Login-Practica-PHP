<?php
require_once "./Modelo/authModelo.php";
require_once "./Modelo/tokenModelo.php";
require_once "./Controlador/respuestas.php";

class authVali extends conexionBd 
{
    //<!-- ========== METODO DE INICIO DE SESION PARA OBTENER TOKEN DE AUTORIZACION ========== -->
    public function login($json)
    {
        $_respuestas = new respuestas;
        $_token = new token;
        $_auth = new auth;
        $datos = json_decode($json, true);
        if (!isset($datos["correo"]) || !isset($datos["password"])) {
            // error en los campos
            return $_respuestas->error_400();
        } else {
            // todo esta bien
            $correo = $datos["correo"];
            $password = $datos["password"];
            $password = parent::encriptar($password);
            $datos = $_auth->obtenerDatosCredencial($correo);
            if ($datos) {
                // Verificar si la contraseña es igual
                if ($password == $datos[0]["password"]) {
                    if ($datos[0]["estado"] == "1") {
                        $verificar = $_token->insertarToken($datos[0]["id"]);
                        if ($verificar) {
                            //Se guardo
                            $result = $_respuestas->response;
                            $result["result"] = array(
                                "token" => $verificar
                            );
                            return $result;
                        } else {
                            //No se guardo
                            return $_respuestas->error_500("Error interno, no se ha podido guardar!!");
                        }
                    } else {
                        //Usuario inactivo
                        return $_respuestas->error_200("Usuario inactivo!!");
                    }
                } else {
                    //Contraseña incorrecta
                    return $_respuestas->error_200("La contraseña es invalida!!");
                }
            } else {
                // Si no existe el usuario
                return $_respuestas->error_200("El usuario $correo no existe!!");
            }
        }
    }
}
