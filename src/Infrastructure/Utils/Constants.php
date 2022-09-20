<?php 

namespace App\Infrastructure\Utils;

class Constants{

    //SUCCESS
    public static $OK_HISTORY = "Historial listado correctamente.";
    public static $LOGIN_SUCCESS = "Usuario logueado correctamente.";
    public static $USER_NOT_FOUND = "Usuario no encontrado.";
    public static $REGISTER_PROFESSOR_SUCCESS = "Profesor registrado correctamente.";
    public static $UPDATE_PROFESSOR_SUCCESS = "Profesor actualizado correctamente.";
    public static $DELETE_PROFESSOR_SUCCESS = "Profesor eliminado correctamente.";

    //ERROR
    public static $ERROR_DB = "Error en la consulta a DB.";
    public static $ERROR_REQUEST = "Error en la peticion.";
    public static $ERROR_EMAIL_EXIST = "El correo que intentas registrar ya existe.";
    public static $ERROR_TOKEN = "Token de la sesion no valido.";
    public static $ERROR_NOT_FOUND = "Elemento no econtrado.";
    
}

?>