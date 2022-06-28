<?php 

namespace App\Application\Actions\Controllers;

use DBController;
use App\Infrastructure\Utils\SecurityJWT;
use App\Infrastructure\Utils\ResponseBody;
use App\Infrastructure\Utils\Constants;

include_once 'DBController.php';

class PanelProfessor{

    public static $response;

    public static function saveGame($data){
        $jsonRequest = json_decode($data);

        PanelProfessor::$response = ResponseBody::setResponse(true, "OK", ResponseBody::$OK);

        return PanelProfessor::$response;
    }

}

?>