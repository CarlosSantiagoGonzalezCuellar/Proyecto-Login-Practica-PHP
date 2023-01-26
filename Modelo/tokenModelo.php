<?php
require_once "./Modelo/Conexion/conexion.php";

class token extends conexionBd
{

    //<!-- ========== METODO PARA AGREGAR EL TOKEN CREADO ========== -->
    public function insertarToken($id)
    {
        $_pdo = new conexionBd;

        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
        $fecha = time() + 86400;
        $fechaExp = date("Y-m-d H:i:s", substr($fecha, 0, 10));
        $estado = "1";

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

    //<-- ========== METODO PARA OBTENER EL TOKEN ========== -->
    public function buscarToken($token)
    {
        $_pdo = new conexionBd;

        $sql = $_pdo->prepare("SELECT * FROM tokens WHERE token = :token AND estado = '1'");
        $sql->bindValue(':token', $token);
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

    //<-- ========== METODO PARA INACTIVAR TOKEN ========== -->
    public function inactivarToken()
    {
        $_pdo = new conexionBd;
        
        $estado = "0";
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

    //<-- ========== METODO PARA VALIDAR EXISTENCIA Y ESTADO DE TOKEN ========== -->
    public function validarToken($token)
    {
        $arrayToken = $this->buscarToken($token);
        if ($arrayToken) {
            return true;
        } else {
            return false;
        }
    }

}
