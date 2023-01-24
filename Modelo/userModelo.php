<?php
require_once "./Modelo/Conexion/conexion.php";

class usuarios extends conexionBd
{
    //<-- ========== LISTAR USUARIOS  ========== -->
    public function listaUsuarios()
    {
        $_pdo = new conexionBd;

        $sql = $_pdo->prepare("SELECT usuarios.id, usuarios.nombre, roles.nombreRol, usuarios.estado
            FROM usuarios
            INNER JOIN roles
            ON usuarios.rol = roles.id
            WHERE usuarios.estado = '1'");
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
            WHERE usuarios.id = :id AND usuarios.estado = '1'");
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

    //<-- ========== METODO PARA AÑADIR NUEVO USUARIO ========== -->
    public function insertarUsuario($nombre, $rol, $estado)
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("INSERT INTO usuarios (nombre, rol, estado) 
        VALUES (:nombre, :rol, :estado)");
        $sql->bindValue(':nombre', $nombre);
        $sql->bindValue(':rol', $rol);
        $sql->bindValue(':estado', $estado);
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

    //<-- ========== METODO PARA MODIFICAR UN USUARIO SEGUN SU ID ========== -->
    public function modificarUsuario($nombre, $rol, $estado, $usuarioId)
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("UPDATE usuarios SET nombre=:nombre, rol=:rol, estado=:estado
        WHERE id=:id");
        $sql->bindValue(':nombre', $nombre);
        $sql->bindValue(':rol', $rol);
        $sql->bindValue(':estado', $estado);
        $sql->bindValue(':id', $usuarioId);
        $sql->execute();

        $resp = $sql;
        if ($resp == true) {
            return $resp;
        } else {
            return 0;
        }
    }

    //<-- ========== METODO PARA INACTIVAR USUARIO SEGUN SU ID ========== -->
    public function eliminarUsuario($estado, $usuarioId)
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("UPDATE usuarios SET estado=:estado
        WHERE id=:id");
        $sql->bindValue(':estado', $estado);
        $sql->bindValue(':id', $usuarioId);
        $sql->execute();

        $resp = $sql;
        if ($resp == true) {
            return $resp;
        } else {
            return 0;
        }
    }
}
