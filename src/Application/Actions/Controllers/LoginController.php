<?php 

namespace App\Application\Actions\Controllers;

use DBController;
use App\Infrastructure\Utils\SecurityJWT;
use App\Infrastructure\Utils\ResponseBody;
use App\Infrastructure\Utils\Constants;

include_once 'DBController.php';

class LoginController{

    public static $response;

    public static function loginAdmin($user, $password){
        if($user != "" && $password != "" && $user != null && $password != null){
            //Validar contra DB.
            $db = new DBController();
            $conexion = $db->getConexion();
    
            $query = $conexion->query("SELECT * FROM admin WHERE BINARY user = '$user' AND BINARY password = '$password'");
            if($query){
                $numRows = mysqli_num_rows($query);
                if($numRows == 1){
                    $dataRow = mysqli_fetch_array($query);
                    //Crear token
                    $token = SecurityJWT::generateToken($dataRow['id_admin'], "ADMIN");
                    //Guardar token
                    LoginController::saveTokenAdmin($token, $dataRow['id_admin'], $conexion);
                    LoginController::$response = ResponseBody::setResponse($token, Constants::$LOGIN_SUCCESS, ResponseBody::$OK);
                }else
                    LoginController::$response = ResponseBody::setResponse(null, Constants::$USER_NOT_FOUND, ResponseBody::$OK);
            }else
                LoginController::$response = ResponseBody::setResponse(null, Constants::$ERROR_DB, ResponseBody::$INTERNAL_SERVER_ERROR);
        }else
            LoginController::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
        
        return LoginController::$response;
    }

    public static function loginProfesor($email, $password){
        if($email != "" && $password != "" && $email != null && $password != null){
            //Validar contra DB.
            $db = new DBController();
            $conexion = $db->getConexion();
            $passMD5 = md5($password);
            $query = $conexion->query("SELECT * FROM professors WHERE BINARY email = '$email' AND BINARY password = '$passMD5'");
            if($query){
                $numRows = mysqli_num_rows($query);
                if($numRows == 1){
                    $dataRow = mysqli_fetch_array($query);
                    //Crear token
                    $token = SecurityJWT::generateToken($dataRow['id_professors'], "PROFESSOR");
                    //Guardar token
                    //LoginController::saveTokenAdmin($token, $dataRow['id_admin'], $conexion);
                    LoginController::$response = ResponseBody::setResponse($token, Constants::$LOGIN_SUCCESS, ResponseBody::$OK);
                }else
                    LoginController::$response = ResponseBody::setResponse(null, Constants::$USER_NOT_FOUND, ResponseBody::$OK);
            }else
                LoginController::$response = ResponseBody::setResponse(null, Constants::$ERROR_DB, ResponseBody::$INTERNAL_SERVER_ERROR);
        }else
            LoginController::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
        
        return LoginController::$response;
    }

    public static function saveTokenAdmin($token, $idAdmin, $conexion){
        $saveQuery = $conexion->query("UPDATE admin SET token = '$token' WHERE id_admin = '$idAdmin'");
    }

}

?>