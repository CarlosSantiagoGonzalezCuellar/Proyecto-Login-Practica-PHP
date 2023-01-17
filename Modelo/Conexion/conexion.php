<?php

class conexionBd extends PDO
{
    private $server;
    private $user;
    private $password;
    private $database;
    private $port;

    function __construct()
    {
        //<!-- ========== ASIGNAR DATOS OBTENIDOS A CADA VARIABLE ========== -->
        $listaDatos = $this->datosConexion();
        foreach ($listaDatos as $key => $value) {
            $this->server = $value["server"];
            $this->user = $value["user"];
            $this->password = $value["password"];
            $this->database = $value["database"];
            $this->port = $value["port"];
        }

        //<!-- ========== CONEXION A LA BD POR MEDIO DE PDO ========== -->
        try {
            parent::__construct(
                "mysql:host={$this->server};dbname={$this->database};port={$this->port}",
                $this->user,
                $this->password
            );
        } catch (PDOException $error1) {
            echo "Ha ocurrido un error, no se ha podido conectar a la BD! " . $error1->getMessage();
            die();
        } catch (PDOException $error2) {
            echo "Error generico! " . $error2->getMessage();
            die();
        }
    }

    //<!-- ========== METODO DE OBTENCION DE CREDENCIALES DEL ARCHIVO CONFIG ========== -->
    private function datosConexion()
    {
        $direccion = dirname(__FILE__);
        $jsonData = file_get_contents($direccion . "/" . "config");
        return json_decode($jsonData, true);
    }

    //<!-- ========== METODO PARA CONVERTIR A UTF8 ========== -->
    public function convertirUtf8($array)
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, "utf-8", true)) {
                $item = iconv("ISO-8859-1", "UTF-8", $item);
            }
        });
        return $array;
    }

    //<!-- ========== METODO PARA ENCRIPTAR CONTRASEÑA DIGITADA A MD5 ========== -->
    //ENCRIPTAR
    protected function encriptar($string){
        return md5($string);
    }
}
