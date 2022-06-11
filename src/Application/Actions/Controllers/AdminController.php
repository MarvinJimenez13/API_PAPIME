<?php 

namespace App\Application\Actions\Controllers;

use App\Infrastructure\Utils\SecurityJWT;
use App\Infrastructure\Utils\ResponseBody;
use App\Infrastructure\Utils\Constants;

include_once 'DBController.php';

class AdminController{

    public static $response;

    public static function saveProfessor($data){
        AdminController::$response = ResponseBody::setResponse($data, null, 200);
        
        return AdminController::$response;
    }

}

?>