<?php 

namespace App\Application\Actions\Controllers;

use DBController;
use App\Infrastructure\Utils\SecurityJWT;
use App\Infrastructure\Utils\ResponseBody;
use App\Infrastructure\Utils\Constants;
use App\Application\Actions\Models\Professor;

include_once 'DBController.php';

class AdminController{

    public static $response;

    public static function eliminarProfesor($data){
        $jsonRequest = json_decode($data);

        if($jsonRequest != null && $jsonRequest->token != null && $jsonRequest->id_professors){
            //Validar y obtener info de token
            if(SecurityJWT::validateSignatureToken($jsonRequest->token)){
                $dataToken = json_decode(SecurityJWT::decryptToken($jsonRequest->token));
                if($dataToken->tipo_usuario == "ADMIN"){
                    $db = new DBController();
                    $conexion = $db->getConexion();

                    if(AdminController::exist($conexion, "professors", "id_professors", $jsonRequest->id_professors)){
                        //ELIMINAR PROFESOR
                        $queryDelete = $conexion->query("DELETE FROM professors WHERE id_professors='$jsonRequest->id_professors'");

                        if($queryDelete)
                            AdminController::$response = ResponseBody::setResponse(true, Constants::$DELETE_PROFESSOR_SUCCESS, ResponseBody::$OK);
                        else
                            AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_DB, ResponseBody::$OK);
                    }else
                        AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_NOT_FOUND, ResponseBody::$BAD_REQUEST);

                }else
                    AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
            }else
                AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_TOKEN, ResponseBody::$BAD_REQUEST);
        }else
            AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);

        return AdminController::$response;
    }

    public static function getProfessors($token){
        if($token != null && $token != ""){
            //Validar y obtener info de token
            if(SecurityJWT::validateSignatureToken($token)){
                $dataToken = json_decode(SecurityJWT::decryptToken($token));
                if($dataToken->tipo_usuario == "ADMIN"){
                    $db = new DBController();
                    $conexion = $db->getConexion();
                    //OBTENEMOS PROFESORES
                    $queryList = $conexion->query("SELECT * FROM professors");
                    if($queryList){
                        $arrayResult = array();
                        while($row = mysqli_fetch_array($queryList)){
                            $professor = new Professor;
                            $professor = $row;
                            array_push($arrayResult, $professor);
                        }

                        AdminController::$response = ResponseBody::setResponse($arrayResult, "Listado de profesores regitrados", ResponseBody::$OK);
                    }else
                        AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_DB, ResponseBody::$OK);
                }else
                    AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
            }else
                AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_TOKEN, ResponseBody::$BAD_REQUEST);
        }else
            AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
 
        return AdminController::$response;
    }

    public static function updateProfessor($data){
        $jsonRequest = json_decode($data);

        if($jsonRequest != null && $jsonRequest->email != null && $jsonRequest->password != null && $jsonRequest->name != null
            && $jsonRequest->last_name != null && $jsonRequest->token != null && $jsonRequest->id_professors && $jsonRequest->email != ""
            && $jsonRequest->password != "" && $jsonRequest->name != "" && $jsonRequest->last_name != "" && $jsonRequest->token != ""
            && $jsonRequest->id_professors != ""){
            //Validar y obtener info de token
            if(SecurityJWT::validateSignatureToken($jsonRequest->token)){
                $dataToken = json_decode(SecurityJWT::decryptToken($jsonRequest->token));
                if($dataToken->tipo_usuario == "ADMIN"){
                    $db = new DBController();
                    $conexion = $db->getConexion();

                    if(AdminController::exist($conexion, "professors", "id_professors", $jsonRequest->id_professors)){
                        //ACTUALIZAR PROFESOR
                        $password = md5($jsonRequest->password);
                        $querySave = $conexion->query("UPDATE professors SET email = '$jsonRequest->email', password = '$password', name = '$jsonRequest->name',
                                                            last_name = '$jsonRequest->last_name' WHERE id_professors = '$jsonRequest->id_professors'");
                        if($querySave)
                            AdminController::$response = ResponseBody::setResponse(true, Constants::$UPDATE_PROFESSOR_SUCCESS, ResponseBody::$OK);
                        else
                            AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_DB, ResponseBody::$OK);
                    }else
                        AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_NOT_FOUND, ResponseBody::$BAD_REQUEST);

                }else
                    AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
            }else 
                AdminController::$response = ResponseBody::setResponse(null, Constants::$ERROR_TOKEN, ResponseBody::$BAD_REQUEST);
        }else
            AdminController::$response = ResponseBody::setResponse($data, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
    
        return AdminController::$response;
    }

    public static function exist($conexion, $table, $param, $value){
        $exist = false;
        $consulta = $conexion->query("SELECT COUNT(*) AS num_exist FROM $table WHERE $param = '$value'");
        if($consulta){
            $row = mysqli_fetch_array($consulta);
            if($row['num_exist'] >= 1)
                $exist = true;
        }

        return $exist;
    }

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
                    $db = new DBController();
                    $conexion = $db->getConexion();
                    //VALIDAR QUE NO EXISTA CORREO
                    if(!AdminController::exist($conexion, "professors", "email", $jsonRequest->email)){
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

}

?>