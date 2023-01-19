<?php
require_once "./Modelo/Conexion/conexion.php";
require_once "./Controlador/respuestas.php";

class auth extends conexionBd
{
    //<!-- ========== METODO DE INICIO DE SESION PARA OBTENER TOKEN DE AUTORIZACION ========== -->
    public function login($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);
        if (!isset($datos["correo"]) || !isset($datos["password"])) {
            // error en los campos
            return $_respuestas->error_400();
        } else {
            // todo esta bien
            $correo = $datos["correo"];
            $password = $datos["password"];
            $password = parent::encriptar($password);
            $datos = $this->obtenerDatosUsuario($correo);
            if ($datos) {
                // Verificar si la contraseña es igual
                if ($password == $datos[0]["password"]) {
                    if ($datos[0]["estado"] == "Activo") {
                        $verificar = $this->insertarToken($datos[0]["id"]);
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

    //<!-- ========== METODO PARA OBTENER CREDENCIALES CON SU CORREO Y CONTRASEÑA ========== -->
    public function obtenerDatosUsuario($correo)
    {
        $_pdo = new conexionBd;

        $sql = $_pdo->prepare("SELECT * FROM credenciales WHERE correo = :correo");
        $sql->bindValue(':correo', $correo);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $sql->fetchAll();
        $resultArray = array();

        foreach ($datos as $key) {
            $resultArray[] = $key;
        }

        $resp = $this->convertirUtf8($resultArray);
        if (isset($resp[0]["id"])) {
            return $resp;
        } else {
            return 0;
        }
    }

    //<!-- ========== METODO PARA AGREGAR EL TOKEN CREADO ========== -->
    private function insertarToken($id)
    {
        $_pdo = new conexionBd;
        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
        $fecha = time() + 86400;
        $fechaExp = date("Y-m-d H:i:s", substr($fecha, 0, 10));
        $estado = "Activo";
        $sql = $_pdo->prepare("INSERT INTO tokens (token, usuario, fecha_expiracion, estado) VALUES (:token, :usuario, :fechaExp, :estado)");
        $sql->bindValue(':token', $token);
        $sql->bindValue(':usuario', $id);
        $sql->bindValue(':fechaExp', $fechaExp);
        $sql->bindValue(':estado', $estado);
        $sql->execute();
        $verifica = $sql;

        if ($verifica) {
            return $token;
        } else {
            return 0;
        }
    }

    //<-- ========== UPDATE ========== -->
    public function inactivarToken()
    {
        $_pdo = new conexionBd;
        $estado = "Inactivo";
        $fecha = date('Y-m-d H:i:s');
        $sql = $_pdo->prepare("UPDATE tokens SET estado=:estado
        WHERE fecha_expiracion < :fecha");
        $sql->bindValue(':estado', $estado);
        $sql->bindValue(':fecha', $fecha);
        $sql->execute();

        $resp = $sql;
        if ($resp == true) {
            return $resp;
        } else {
            return 0;
        }
    }

    //****************************************************************************************
    //<!-- ========== CRUD AUTH ========== -->
    private $tokenId = "";
    private $token = "";
    private $usuario = "";
    private $fechaExp = "00/00/000";
    private $estado = "";

    //<-- ========== CREATE ========== -->
    private function createAuth()
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("INSERT INTO tokens (token, usuario, fecha_expiracion, estado) 
        VALUES (:token, :usuario, :fechaExp, :estado)");
        $sql->bindValue(':token', $this->token);
        $sql->bindValue(':usuario', $this->usuario);
        $sql->bindValue(':fechaExp', $this->fechaExp);
        $sql->bindValue(':estado', $this->estado);
        $sql->execute();
        $respuesta = $sql;
        if ($respuesta == true) {
            $resp = $_pdo->lastInsertId();
        } else {
            $resp = 0;
        }

        if ($resp) {
            return $resp;
        } else {
            return 0;
        }
    }

    //<-- ========== READ ========== -->
    public function readAuth($id)
    {
        $_pdo = new conexionBd;

        $sql = $_pdo->prepare("SELECT tokens.token, credenciales.correo, tokens.fecha_expiracion, tokens.estado
            FROM tokens
            INNER JOIN credenciales
            ON tokens.usuario = credenciales.id
            WHERE tokens.id = :id AND tokens.estado = 'Activo'");
        $sql->bindValue(':id', $id);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $sql->fetchAll();
        $resultArray = array();

        foreach ($datos as $key) {
            $resultArray[] = $key;
        }
        $resp = $this->convertirUtf8($resultArray);
        return $resp;
    }


    //<-- ========== UPDATE ========== -->
    private function updateAuth()
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("UPDATE tokens SET token=:token, usuario=:usuario, fecha_expiracion=:fechaExp, estado=:estado
        WHERE id=:id");
        $sql->bindValue(':token', $this->token);
        $sql->bindValue(':usuario', $this->usuario);
        $sql->bindValue(':fechaExp', $this->fechaExp);
        $sql->bindValue(':estado', $this->estado);
        $sql->bindValue(':id', $this->tokenId);
        $sql->execute();

        $resp = $sql;
        if ($resp == true) {
            return $resp;
        } else {
            return 0;
        }
    }

    
    //<-- ========== DELETE ========== -->
    private function deleteAuth()
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("UPDATE tokens SET estado=:estado
        WHERE id=:id");
        $sql->bindValue(':estado', $this->estado);
        $sql->bindValue(':id', $this->tokenId);
        $sql->execute();

        $resp = $sql;
        if ($resp == true) {
            return $resp;
        } else {
            return 0;
        }
    }
}
