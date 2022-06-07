<?php 

namespace App\Application\Actions\Controllers;

use App\Application\Actions\Models\Professor;
use DBController;

include_once 'DBController.php';

class DemoController{

    public static function getDemoModel(){

        $db = new DBController();
        $conexion = $db->getConexion();


        $professor = new Professor();
        $professor->name = "HI PAPIME!";
        $professor->last_name = "TEEEEST HEROKU";

        return $professor;
    }

}


?>