<?php 


include_once 'DBController.php';

class DBController{

    public $DB_HOST = "localhost"; //162.241.62.137
    public $DB_USUARIO = "root"; //  id19261929_admin
    public $DB_CONTRASENA = ""; // Apipapime_2022
    public $DB = "papime"; //  id19261929_papime
    public $CHARSET = "UTF8";
    public $conexion;

    public function getConexion(){
        $this->conexion = mysqli_connect($this->DB_HOST, $this->DB_USUARIO, $this->DB_CONTRASENA);
        if(mysqli_connect_errno())
            return "Error en la conexión: ". mysqli_connect_error();

        mysqli_set_charset($this->conexion, $this->CHARSET);
        mysqli_select_db($this->conexion, $this->DB) or die("Error en la conexion");
        
         return $this->conexion;
    }

}
?>