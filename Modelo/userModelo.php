<?php
require_once "./Modelo/Conexion/conexion.php";
require_once "./Controlador/respuestas.php";

class usuarios extends conexionBd
{
    private $usuarioId = "";
    private $nombre = "";
    private $rol = "";
    private $estado = "";
    private $token = "";

    //<-- ========== LISTAR USUARIOS  ========== -->
    public function listaUsuarios()
    {
        $_pdo = new conexionBd;

        $sql = $_pdo->prepare("SELECT usuarios.id, usuarios.nombre, roles.nombreRol, usuarios.estado
            FROM usuarios
            INNER JOIN roles
            ON usuarios.rol = roles.id
            WHERE usuarios.estado = 'Activo'");
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

    //<-- ========== OBTENER USUARIO EN ESPECIFIO SEGUN SU ID ========== -->
    public function obtenerUsuario($id)
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("SELECT usuarios.id, usuarios.nombre, roles.nombreRol, usuarios.estado
            FROM usuarios
            INNER JOIN roles
            ON usuarios.rol = roles.id
            WHERE usuarios.id = :id AND usuarios.estado = 'Activo'");
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

    //<-- ========== METODO POST CON VALIDACIONES ========== -->
    public function post($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if (!isset($datos["token"])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos["token"];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                if (!isset($datos["nombre"]) || !isset($datos["rol"]) || !isset($datos["estado"])) {
                    return $_respuestas->error_400();
                } else {
                    $this->nombre = $datos["nombre"];
                    $this->rol = $datos["rol"];
                    $this->estado = $datos["estado"];
                    $resp = $this->insertarUsuario();
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

    //<-- ========== METODO PARA AÑADIR NUEVO USUARIO ========== -->
    private function insertarUsuario()
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("INSERT INTO usuarios (nombre, rol, estado) 
        VALUES (:nombre, :rol, :estado)");
        $sql->bindValue(':nombre', $this->nombre);
        $sql->bindValue(':rol', $this->rol);
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

    //<-- ========== METODO PATCH CON VALIDACIONES ========== -->
    public function patch($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if (!isset($datos["token"])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos["token"];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
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
                    if (isset($datos["estado"])) {
                        $this->estado = $datos["estado"];
                    }
                    $resp = $this->modificarUsuario();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $this->usuarioId
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

    //<-- ========== METODO PARA MODIFICAR UN USUARIO SEGUN SU ID ========== -->
    private function modificarUsuario()
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("UPDATE usuarios SET nombre=:nombre, rol=:rol, estado=:estado
        WHERE id=:id");
        $sql->bindValue(':nombre', $this->nombre);
        $sql->bindValue(':rol', $this->rol);
        $sql->bindValue(':estado', $this->estado);
        $sql->bindValue(':id', $this->usuarioId);
        $sql->execute();

        $resp = $sql;
        if ($resp == true) {
            return $resp;
        } else {
            return 0;
        }
    }

    //<-- ========== METODO DELETE CON VALIDACIONES ========== -->
    public function delete($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if (!isset($datos["token"])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos["token"];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                if (!isset($datos["id"])) {
                    return $_respuestas->error_400();
                } else {
                    $this->usuarioId = $datos["id"];
                    $this->estado = "Inactivo";

                    $resp = $this->eliminarUsuario();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $this->usuarioId
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

    //<-- ========== METODO PARA INACTIVAR USUARIO SEGUN SU ID ========== -->
    private function eliminarUsuario()
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("UPDATE usuarios SET estado=:estado
        WHERE id=:id");
        $sql->bindValue(':estado', $this->estado);
        $sql->bindValue(':id', $this->usuarioId);
        $sql->execute();

        $resp = $sql;
        if ($resp == true) {
            return $resp;
        } else {
            return 0;
        }
    }

    //<-- ========== METODO PARA OBTENER EL TOKEN ========== -->
    private function buscarToken()
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("SELECT * FROM tokens WHERE token = :token AND estado = 'Activo'");
        $sql->bindValue(':token', $this->token);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $sql->fetchAll();
        $resultArray = array();

        foreach ($datos as $key) {
            $resultArray[] = $key;
        }
        $resp = $this->convertirUtf8($resultArray);
        if ($resp) {
            return $resp;
        } else {
            return 0;
        }
    }
}
