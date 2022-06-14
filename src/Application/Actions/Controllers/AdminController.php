<?php 

namespace App\Application\Actions\Controllers;

use DBController;
use App\Infrastructure\Utils\SecurityJWT;
use App\Infrastructure\Utils\ResponseBody;
use App\Infrastructure\Utils\Constants;

include_once 'DBController.php';

class AdminController{

    public static $response;

    public static function saveProfessor($data){
        $jsonRequest = json_decode($data);
        
        if($jsonRequest != null && $jsonRequest->email != null && $jsonRequest->password != null && $jsonRequest->name != null
            && $jsonRequest->last_name != null && $jsonRequest->token != null && $jsonRequest->email != ""
             && $jsonRequest->password != "" && $jsonRequest->name != "" && $jsonRequest->last_name != ""
              && $jsonRequest->token != ""){
            //Validar y obtener info de token
            if(SecurityJWT::validateSignatureToken($jsonRequest->token)){
                $dataToken = json_decode(SecurityJWT::decryptToken($jsonRequest->token));
                if($dataToken->tipo_usuario == "ADMIN"){
                    //Guardamos
                    $db = new DBController();
                    $conexion = $db->getConexion();
                    //VALIDAR QUE NO EXISTA CORREO
                    if(!AdminController::emailExist($jsonRequest->email, $conexion)){
                        $password = md5($jsonRequest->password);
                        $querySave = $conexion->query("INSERT INTO professors (email, password, name, last_name, id_admin_register) 
                        VALUES ('$jsonRequest->email', '$password', '$jsonRequest->name',
                                     '$jsonRequest->last_name','$dataToken->id')");

                        if($querySave)
                            AdminController::$response = ResponseBody::setResponse(true, Constants::$REGISTER_PROFESSOR_SUCCESS, ResponseBody::$OK);
                        else
                            AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_DB, ResponseBody::$OK);
                    }else
                        AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_EMAIL_EXIST, ResponseBody::$OK);
                }else
                    AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
            }else 
                AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_TOKEN, ResponseBody::$BAD_REQUEST);
        }else
            AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
        
        return AdminController::$response;
    }

    public static function emailExist($email, $conexion){
        $queryValidate = $conexion->query("SELECT COUNT(*) AS num_exist FROM professors WHERE email = '$email'");
        if($queryValidate){
            $rowData = mysqli_fetch_array($queryValidate);
            if($rowData['num_exist'] >= 1)
                return true;
            else return false;
        }

        return true;
    }

}

?>