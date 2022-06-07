<?php 

//include_once '../servicios/Constants.php';

class DBController{

    public $DB_HOST = "162.241.62.137"; //162.241.62.187
    public $DB_USUARIO = "comer208"; //urvanmx
    public $DB_CONTRASENA = "gus750819";//Uvt200430*
    public $DB = "comer208_papime";
    public $CHARSET = "UTF8";
    public $conexion;

    public function getConexion(){
        $this->conexion = mysqli_connect($this->DB_HOST, $this->DB_USUARIO, $this->DB_CONTRASENA);
        if(mysqli_connect_errno())
            return "Error en la conexión: ". mysqli_connect_error();

        mysqli_set_charset($this->conexion, $this->CHARSET);
        mysqli_select_db($this->conexion, $this->DB) or die("Error");
        
         return $this->conexion;
    }

}
?>