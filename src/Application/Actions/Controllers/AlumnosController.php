<?php 

namespace App\Application\Actions\Controllers;

use DBController;
use App\Infrastructure\Utils\ResponseBody;
use App\Infrastructure\Utils\Constants;

include_once 'DBController.php';

class AlumnosController{

    public static $response;

    public static function getJuego($codigo){
        $db = new DBController();
        $conexion = $db->getConexion();


        //Obtenemos informacion principal de la tabla sessions para la primera parte del JSON.
        $querySession = $conexion->query("SELECT * FROM sessions WHERE code = '$codigo'");
        if($querySession){
            $dataSession = mysqli_fetch_array($querySession);

            //TODO Validamos si existe el codigo o no.
            if($dataSession == null){
                AlumnosController::$response = ResponseBody::setResponse(null, "No se encontró el juego, verifica el código.", ResponseBody::$BAD_REQUEST);
            }else{
                $dataJson = array(
                    "grupo" => $dataSession['group_num'],
                    "situacionEnsenanza" => $dataSession['teaching_situation'],
                    "materia" => $dataSession['course'],
                    "nombreSesion" => $dataSession['session_name'],
                    "codigo" => $codigo,
                    "game" => AlumnosController::getRooms($dataSession['id_sessions'], $conexion)
                );
                AlumnosController::$response = ResponseBody::setResponse($dataJson, "OK", ResponseBody::$OK);
            }

        }else
            AlumnosController::$response = ResponseBody::setResponse(null, Constants::$ERROR_DB, ResponseBody::$OK);

        return AlumnosController::$response;
    }

    public static function getRooms($idSession, $conexion){
        $queryRooms = $conexion->query("SELECT * FROM rooms WHERE sessions_id_sessions = '$idSession'");

        $aux = 1;
        $room1 = null;
        $room2 = null;
        $room3 = null;
        $room4 = null;

        while($row = mysqli_fetch_array($queryRooms)){
            switch ($aux) {
                case 1:
                    $room1 = array(
                        "name" => $row['name'], 
                        "num_questions" => $row['num_questions'],
                        "has_images" => AlumnosController::isImage($row['images']),
                        "questions" => []);
                    break;
                case 2:
                    $room2 = array(
                        "name" => $row['name'], 
                        "num_questions" => $row['num_questions'],
                        "has_images" => AlumnosController::isImage($row['images']),
                        "questions" => []);
                    break;
                case 3:
                    $room3 = array(
                        "name" => $row['name'], 
                        "num_questions" => $row['num_questions'],
                        "has_images" => AlumnosController::isImage($row['images']),
                        "questions" => []);
                    break;
                case 4:
                    $room4 = array(
                        "name" => $row['name'], 
                        "num_questions" => $row['num_questions'],
                        "has_images" => AlumnosController::isImage($row['images']),
                        "questions" => []);
                    break;
            }
            $aux++;
        }

        $dataJson = array(
            "sala1" => $room1,
            "sala2" => $room2,
            "sala3" => $room3,
            "sala4" => $room4
        );

        return $dataJson;
    }

    public static function isImage($value){
        if($value == 1)
            return true;
        else return false;
    }

}


?>