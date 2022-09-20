<?php 

namespace App\Application\Actions\Controllers;

use DBController;
use App\Infrastructure\Utils\SecurityJWT;
use App\Infrastructure\Utils\ResponseBody;
use App\Infrastructure\Utils\Constants;

include_once 'DBController.php';

class PanelProfessor{

    public static $response;
    public static $code;
    public static $linkParseGame;

    public static function historyGames($token){
        if($token != null){
            //Validar y obtener info de token
            if(SecurityJWT::validateSignatureToken($token)){
                $dataToken = json_decode(SecurityJWT::decryptToken($token));
                if($dataToken->tipo_usuario == "PROFESSOR"){//cambiar a profesor cuando tengamos login
                    $db = new DBController();
                    $conexion = $db->getConexion();

                    //creamos objeto historial
                    $historial = array();

                    //Consultamos tabla sessions
                    $querySessions = $conexion->query("SELECT * FROM sessions WHERE id_professor = '$dataToken->id'");
                    if($querySessions){
                        while($row = mysqli_fetch_array($querySessions)){
                            $arraySession = array(
                                "idSession" => $row['id_sessions'],
                                "grupo" => $row['group_num'],
                                "situacionEnsenanza" => $row['teaching_situation'],
                                "materia" => $row['course'],
                                "nombreSesion" => $row['session_name'],
                                "expiracion" => $row['expiration'],
                                "link" => $row['link'],
                                "code" => $row['code'] 
                            );

                            array_push($historial, $arraySession);
                        }

                        $dataHistory = array("history" => $historial);
                        PanelProfessor::$response = ResponseBody::setResponse($dataHistory, Constants::$OK_HISTORY, ResponseBody::$OK);
                    }else
                        LoginController::$response = ResponseBody::setResponse(null, Constants::$ERROR_DB, ResponseBody::$INTERNAL_SERVER_ERROR);
                }else
                    PanelProfessor::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
            }else
                PanelProfessor::$response = ResponseBody::setResponse(null, Constants::$ERROR_TOKEN, ResponseBody::$BAD_REQUEST);
        }else
            PanelProfessor::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);

        return PanelProfessor::$response;
    }

    public static function saveGame($data){
        $dataJSON = array("data" => json_decode($data));
        
        
        if($data != null && $dataJSON['data']->token != null){
            //Validar y obtener info de token
            
            if(SecurityJWT::validateSignatureToken($dataJSON['data']->token)){
                $dataToken = json_decode(SecurityJWT::decryptToken($dataJSON['data']->token));
                
                
                if($dataToken->tipo_usuario == "PROFESSOR"){//cambiar a profesor cuando tengamos login
                    
                    //GENERAMOS LINK Y CODIGO
                    $code = PanelProfessor::getCodeGame();
                    PanelProfessor::$code = $code;
                    $linkGame = "https://papime.unam.mx/juego/".$code;
                    PanelProfessor::$linkParseGame = "https:/"."/"."papime.unam.mx"."/"."juego"."/".$code;
                    
                    //GUARDAMOS DATOS EN SESSIONS
                    $db = new DBController();
                    $conexion = $db->getConexion();
                    
                    $response = PanelProfessor::saveSessions($conexion, $dataJSON['data'], $linkGame, $code, $dataToken->id);
                    if($response != -1){
                        //PROCESAMOS GAME
                        $responseGame = PanelProfessor::processRooms($conexion, $dataJSON['data']->game, $response);
                    }else 
                        PanelProfessor::$response = ResponseBody::setResponse(null, "Error al guardar datos", ResponseBody::$BAD_REQUEST); //ERROR POR DEFINIR
                }else
                    PanelProfessor::$response = ResponseBody::setResponse(null, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
            }else
                PanelProfessor::$response = ResponseBody::setResponse(null, Constants::$ERROR_TOKEN, ResponseBody::$BAD_REQUEST);
        }else 
            PanelProfessor::$response = ResponseBody::setResponse($dataJSON['data']->token, Constants::$ERROR_REQUEST, ResponseBody::$BAD_REQUEST);
        
        return json_encode(PanelProfessor::$response);

        //return $dataJSON['data']->game->sala1->name;
    }

    public static function processRooms($conexion, $jsonRequest, $idSession){
        //VALIDAR SALA1
        $idSala1 = PanelProfessor::saveRoom($conexion, $jsonRequest->sala1, $idSession);
        PanelProfessor::$response = ResponseBody::setResponse(null, "pkas7", ResponseBody::$OK);
        if($idSala1 != 1){
        
            //VALIDAMOS PREGUNTAS
            PanelProfessor::validateQuestions(
                $conexion, $jsonRequest->sala1->num_questions,
                 $jsonRequest->sala1->questions,
                  $idSala1);
            //VALIDAR SALA2
            $idSala2 = PanelProfessor::saveRoom($conexion, $jsonRequest->sala2, $idSession);
            if($idSala2 != 1){
                //VALIDAMOS PREGUNTAS
                PanelProfessor::validateQuestions(
                    $conexion, $jsonRequest->sala2->num_questions,
                     $jsonRequest->sala2->questions,
                      $idSala2);
                //VALIDAR SALA3
                $idSala3 = PanelProfessor::saveRoom($conexion, $jsonRequest->sala3, $idSession);
                if($idSala3 != 1){
                    //VALIDAMOS PREGUNTAS
                    PanelProfessor::validateQuestions(
                        $conexion, $jsonRequest->sala3->num_questions,
                         $jsonRequest->sala3->questions,
                          $idSala3);
                    //VALIDAR SALA4
                    $idSala4 = PanelProfessor::saveRoom($conexion, $jsonRequest->sala4, $idSession);
                    if($idSala4 != 1){
                        //VALIDAMOS PREGUNTAS
                        PanelProfessor::validateQuestions(
                            $conexion, $jsonRequest->sala4->num_questions,
                             $jsonRequest->sala4->questions,
                              $idSala4);
                    }else
                        PanelProfessor::$response = ResponseBody::setResponse(null, Constants::$ERROR_NOT_FOUND, ResponseBody::$INTERNAL_SERVER_ERROR);
                }else
                    PanelProfessor::$response = ResponseBody::setResponse(null, Constants::$ERROR_NOT_FOUND, ResponseBody::$INTERNAL_SERVER_ERROR);
            }else
                PanelProfessor::$response = ResponseBody::setResponse(null, Constants::$ERROR_NOT_FOUND, ResponseBody::$INTERNAL_SERVER_ERROR);
        }else
            PanelProfessor::$response = ResponseBody::setResponse(null, Constants::$ERROR_NOT_FOUND, ResponseBody::$INTERNAL_SERVER_ERROR);
    }

    public static function validateQuestions($conexion, $num_questions, $questions, $idRoom){
        $aux = 0;
        while($aux < $num_questions){
            $questionInfo = $questions[$aux];
            $num_responses = $questionInfo->responses->num_responses;
            PanelProfessor::$response = ResponseBody::setResponse(null, $idRoom, ResponseBody::$BAD_REQUEST); 
            $aux++;
            
            $querySave = $conexion->query("INSERT INTO questions (question, mul_option, num_responses, rooms_id_rooms)
            VALUES ('$questionInfo->text', '$questionInfo->mul_option', '$num_responses', '$idRoom')");
       

            //OBTENEMOS SU ID
            $idQuestion = PanelProfessor::getIDQuestion($conexion, $idRoom, $questionInfo->text);
            //PROCESAMOS RESPUESTAS DE PREGUNTA N
            PanelProfessor::saveResponses($conexion, $idQuestion, $questionInfo->responses);
        }

        
        PanelProfessor::$response = ResponseBody::setResponse(
            array("code" => PanelProfessor::$code, "link" => utf8_encode(PanelProfessor::$linkParseGame)),
            "Datos guardados correctamente.",
             ResponseBody::$OK);
    }

    public static function saveResponses($conexion, $idQuestion, $responses){
        $isCorrect1 = false;
        $isCorrect2 = false;
        $isCorrect3 = false;
        $isCorrect4 = false;

        if($responses->correct == 1)
            $isCorrect1 = true;
        else if($responses->correct == 2)
            $isCorrect2 = true;
        else if($responses->correct == 3)
            $isCorrect3 = true;
        else if($responses->correct == 4)
            $isCorrect4 = true;

        //GUARDAMOS LA R1
        $querySave = $conexion->query("INSERT INTO answers (questions_id_question, answer, is_correct) VALUES 
                                            ('$idQuestion', '$responses->r1', '$isCorrect1')");
        //GUARDAMOS LA R2
        $querySave = $conexion->query("INSERT INTO answers (questions_id_question, answer, is_correct) VALUES 
                                            ('$idQuestion', '$responses->r2', '$isCorrect2')");

        //GUARDAMOS LA R3
        $querySave = $conexion->query("INSERT INTO answers (questions_id_question, answer, is_correct) VALUES 
                                            ('$idQuestion', '$responses->r3', '$isCorrect3')");

        //GUARDAMOS LA R4
        $querySave = $conexion->query("INSERT INTO answers (questions_id_question, answer, is_correct) VALUES 
                                            ('$idQuestion', '$responses->r4', '$isCorrect4')");

    }

    public static function getIDQuestion($conexion, $idRoom, $question){
        $queryGET = $conexion->query("SELECT id_question FROM questions WHERE rooms_id_rooms = '$idRoom' AND question = '$question'");
        $row = mysqli_fetch_array($queryGET);
        
        return $row['id_question'];
    }

    public static function saveRoom($conexion, $jsonRequest, $idSession){
        $querySave = $conexion->query("INSERT INTO rooms (num_questions, images, name, sessions_id_sessions) VALUES
                                            ('$jsonRequest->num_questions', '$jsonRequest->has_images', '$jsonRequest->name', '$idSession')");

        if($querySave){
            //Obtenemos id   
            $getIDQuery = $conexion->query("SELECT id_rooms FROM rooms WHERE name = '$jsonRequest->name' AND sessions_id_sessions = '$idSession'");
            $row = mysqli_fetch_array($getIDQuery);

            return $row['id_rooms'];
        }else
            return -1;
    }

    public static function saveSessions($conexion, $jsonRequest, $linkGame, $code, $idProfesor){
        $querySave = $conexion->query("INSERT INTO sessions (id_professor, group_num, link, teaching_situation,
                                            session_name, expiration, code, course) VALUES
                                            ($idProfesor, '$jsonRequest->group_num', '$linkGame', '$jsonRequest->teaching_situation',
                                            '$jsonRequest->session_name', '2022/12/31', '$code', '$jsonRequest->course')");

        if($querySave){
            //Obtenemos id   
            $getIDQuery = $conexion->query("SELECT id_sessions FROM sessions WHERE code = '$code'");
            $row = mysqli_fetch_array($getIDQuery);

            return $row['id_sessions'];
        }else
            return -1;
    }

    public static function getCodeGame(){
        $caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 8;
        return substr(str_shuffle($caracteres_permitidos), 0, $longitud);
    }

}

?>