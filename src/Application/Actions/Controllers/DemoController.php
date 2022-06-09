<?php 

namespace App\Application\Actions\Controllers;

use App\Application\Actions\Models\Professor;
use DBController;
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Exceptions\InvalidSignatureException;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;

include_once 'DBController.php';

class DemoController{

    public static function decryptToken($token){
        $result = "";
    
        // Use HS256
        $signer = new HS256('12345678901234567890123456789012');
        $parser = new Parser($signer);

        if(DemoController::validateSignatureToken($token, $parser))
            $result = $parser->parse($token);
        else
            $result = "Token no valido";
        
        return $result;
    }

    private static function validateSignatureToken($token, $parser){
        $valido = false;

        try{
            $parser->verify($token);
            $valido = true;
        }catch(InvalidSignatureException $e){
            $valido = false;
        }

        return $valido;
    }

    public static function getToken($id, $user){
        // Use HS256
        $signer = new HS256('12345678901234567890123456789012');
        // Generate a token
        $generator = new Generator($signer);
        $jwt = $generator->generate(['id' => $id, 'user' => $user]);

        return $jwt;
    }

    public static function testRemoteDB(){

        $db = new DBController();
        $conexion = $db->getConexion();

        $query = $conexion->query("SELECT * FROM admin");
        $professor = new Professor();

        if($query){
            while($row = mysqli_fetch_array($query)){
                $professor->name = $row['name'];
                $professor->last_name = $row['last_name'];
            }
        }

        return $professor;
    }

}


?>