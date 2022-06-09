<?php 

namespace App\Infrastructure\Utils;

class ResponseBody{

    public static $BAD_REQUEST = 400;
    public static $INTERNAL_SERVER_ERROR = 500;
    public static $OK = 200;

    public static function setResponse($data, $message, $code){
        $response = array(
            "body" => $data,
            "message" => $message,
            "response_code" => $code);

        return $response;
    }

}

?>