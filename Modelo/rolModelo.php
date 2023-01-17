<?php
require_once "./Modelo/Conexion/conexion.php";
require_once "./Controlador/respuestas.php";

class rol extends conexionBd
{

    //****************************************************************************************
    //<!-- ========== CRUD AUTH ========== -->
    private $rolId = "";
    private $nombreRol = "";

    //<-- ========== CREATE ========== -->
    private function createAuth()
    {
        $_pdo = new conexionBd;
        $sql = $_pdo->prepare("INSERT INTO roles (nombreRol) 
        VALUES (:nombreRol)");
        $sql->bindValue(':nombreRol', $this->nombreRol);
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
    public function readAuth()
    {
        $_pdo = new conexionBd;

        $sql = $_pdo->prepare("SELECT * FROM roles");
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
        $sql = $_pdo->prepare("UPDATE roles SET nombreRol=:nombreRol
        WHERE id=:id");
        $sql->bindValue(':nombreRol', $this->nombreRol);
        $sql->bindValue(':id', $this->rolId);
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
        $sql = $_pdo->prepare("DELETE FROM roles WHERE id=:id");
        $sql->bindValue(':id', $this->rolId);
        $sql->execute();

        $resp = $sql;
        if ($resp == true) {
            return $resp;
        } else {
            return 0;
        }
    }
}
