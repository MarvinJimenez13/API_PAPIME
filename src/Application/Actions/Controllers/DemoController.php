<?php 

namespace App\Application\Actions\Controllers;

use App\Application\Actions\Models\Professor;

class DemoController{

    public static function getDemoModel(){
        $professor = new Professor();
        $professor->name = "HI PAPIME!";
        $professor->last_name = "TEEEEST HEROKU";

        return $professor;
    }

}


?>