<?php

namespace App\Infrastructure\Utils;

use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Exceptions\InvalidSignatureException;
use MiladRahimi\Jwt\Exceptions\InvalidTokenException;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;

class SecurityJWT{

    public static function decryptToken($token){
        $result = "";
    
        // Use HS256
        $signer = new HS256('12345678901234567890123456789012');
        $parser = new Parser($signer);

        if(SecurityJWT::validateSignatureToken($token, $parser))
            $result = $parser->parse($token);
        else
            $result = "Token no valido";
        
        return json_encode($result);
    }

    public static function generateToken($id, $tipo){
        // Use HS256
        $signer = new HS256('12345678901234567890123456789012');
        // Generate a token
        $generator = new Generator($signer);
        $jwt = $generator->generate(['id' => $id, 'tipo_usuario' => $tipo]);

        return $jwt;
    }

    public static function validateSignatureToken($token){
        // Use HS256
        $signer = new HS256('12345678901234567890123456789012');
        $parser = new Parser($signer);
        $valido = false;

        try{
            $parser->verify($token);
            $valido = true;
        }catch(InvalidSignatureException $e){
            $valido = false;
        }catch(InvalidTokenException $e){
            $valido = false;
        }

        return $valido;
    }

}

?>