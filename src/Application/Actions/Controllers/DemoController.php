<?php 

namespace App\Application\Actions\Controllers;

use App\Application\Actions\Models\Professor;
use DBController;

include_once 'DBController.php';

class DemoController{

    public static function getDemoModel(){

        $db = new DBController();
        $conexion = $db->getConexion();

        $query = $conexion->query("SELECT * FROM admin");
        $professor = new Professor();

        if($query){
            while($row = mysqli_fetch_array($query)){
                $professor->name = $row['name'];
                $professor->last_name = $row['last_name'];
            }
        }

        return $professor;
    }

}


?>