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

    public static function IsAutenticated() {
      try {
          $result = Auth::check();
          if($result){
            return ["Code" => CodeSuccess, "message" => ""];
          }
          else{
            return ["Code" => CodeExpired, "message" => "La sesión ha cadudado"];
          }
      }
      catch (Exception $e) {
          Auth::logout();
          return ["Code" => CodeError, "message" => "Ocurrió un error al validar si el usuario está autenticado, {$e->getMessage()}."];
      }
    }

    public static function logout() {
      try {
          Auth::logout();
          return ["Code" => CodeSuccess, "message" => ""];
      }
      catch (Exception $e) {
          Auth::logout();
          return ["Code" => CodeError, "message" => "Ocurrió un error al cerrar la sesión del usuario, {$e->getMessage()}."];
      }
    }
    
    public static function getUserById($id){
      $users = self::find($id);
      if(!empty($users)){
          return ["Code" => CodeSuccess, "User" => $users[0]];
      }
      return ["Code" => CodeNotFound, "message" => "No se encontró un usuario con el id: {$id}."];
    }

  }
?>
