<?php
require_once "./Modelo/Conexion/conexion.php";
require_once "./Controlador/respuestas.php";
require_once "./Modelo/authModelo.php";
require_once "./Modelo/userModelo.php";

class userValidate extends conexionBd
{
    private $usuarioId = "";
    private $nombre = "";
    private $rol = "";
    private $estado = "";
    private $token = "";

    //<-- ========== METODO POST CON VALIDACIONES ========== -->
    public function post($json)
    {
        $_respuestas = new respuestas;
        $_auth = new auth;
        $_user = new usuarios;
        $datos = json_decode($json, true);

        if (!isset($datos["token"])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos["token"];
            if ($_auth->validarToken($this->token) == true) {
                if (!isset($datos["nombre"]) || !isset($datos["rol"])) {
                    return $_respuestas->error_400();
                } else {
                    $this->nombre = $datos["nombre"];
                    $this->rol = $datos["rol"];
                    $this->estado = "1";
                    $resp = $_user->insertarUsuario($this->nombre, $this->rol, $this->estado);
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $resp
                        );
                        return $respuesta;
                    } else {
                        return $_respuestas->error_500();
                    }
                }
            } else {
                return $_respuestas->error_401("El token enviado es invalido o ha caducado!!");
            }
        }
    }

    //<-- ========== METODO PATCH CON VALIDACIONES ========== -->
    public function patch($json)
    {
        $_respuestas = new respuestas;
        $_auth = new auth;
        $_user = new usuarios;
        $datos = json_decode($json, true);

        if (!isset($datos["token"])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos["token"];
            if ($_auth->validarToken($this->token) == true) {
                if (!isset($datos["id"])) {
                    return $_respuestas->error_400();
                } else {
                    $this->usuarioId = $datos["id"];

                    if (isset($datos["nombre"])) {
                        $this->nombre = $datos["nombre"];
                    }
                    if (isset($datos["rol"])) {
                        $this->rol = $datos["rol"];
                    }
                    $this->estado = "1";
                    $datos = $_user->obtenerUsuario($this->usuarioId);
                    if ($datos) {
                        $resp = $_user->modificarUsuario($this->nombre, $this->rol, $this->estado, $this->usuarioId);
                        if ($resp) {
                            $respuesta = $_respuestas->response;
                            $respuesta["result"] = array(
                                "id" => $this->usuarioId
                            );
                            return $respuesta;
                        } else {
                            return $_respuestas->error_500();
                        }
                    } else {
                        return $_respuestas->error_200("Usuario inactivo!!");
                    }
                }
            } else {
                return $_respuestas->error_401("El token enviado es invalido o ha caducado!!");
            }
        }
    }

    //<-- ========== METODO DELETE CON VALIDACIONES ========== -->
    public function delete($json)
    {
        $_respuestas = new respuestas;
        $_auth = new auth;
        $_user = new usuarios;
        $datos = json_decode($json, true);

        if (!isset($datos["token"])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos["token"];
            if ($_auth->validarToken($this->token) == true) {
                if (!isset($datos["id"])) {
                    return $_respuestas->error_400();
                } else {
                    $this->usuarioId = $datos["id"];
                    $this->estado = "0";
                    $datos = $_user->obtenerUsuario($this->usuarioId);
                    if ($datos) {
                        $resp = $_user->eliminarUsuario($this->estado, $this->usuarioId);
                        if ($resp) {
                            $respuesta = $_respuestas->response;
                            $respuesta["result"] = array(
                                "id" => $this->usuarioId
                            );
                            return $respuesta;
                        } else {
                            return $_respuestas->error_500();
                        }
                    } else {
                        return $_respuestas->error_200("Usuario inactivo!!");
                    }
                }
            } else {
                return $_respuestas->error_401("El token enviado es invalido o ha caducado!!");
            }
        }
    }
}
