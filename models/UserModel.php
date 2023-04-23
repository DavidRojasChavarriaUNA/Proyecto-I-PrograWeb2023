<?php

  include_once('Codes.php');

  class UserModel extends Model {
    protected static $table="users";

    public static function Autenticate($userToAutenticate) {
        try {
            $result = Auth::attempt($userToAutenticate);
            if($result){
              return ["Code" => CodeSuccess, "message" => ""];
            }
            else{
              return ["Code" => CodeUnautorized, "message" => "Usuario o contraseña inválido."];
            }
        }
        catch (Exception $e) {
            return ["Code" => CodeError, "message" => "Ocurrió un error al autenticar el usuario, {$e->getMessage()}."];
        }
    }

  }
?>
