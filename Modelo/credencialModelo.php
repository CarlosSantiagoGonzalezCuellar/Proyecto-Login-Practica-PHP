<?php
require_once "./Modelo/Conexion/conexion.php";
require_once "./Controlador/respuestas.php";

class credencial extends conexionBd
{
    //<!-- ========== METODO PARA OBTENER CREDENCIALES CON SU CORREO Y CONTRASEÑA ========== -->
    public function obtenerDatosCredencial($correo)
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
}

?>