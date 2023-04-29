<?php

  include_once('Codes.php');
  include_once('Util.php');
  include_once('SqLiteSequenceModel.php');

  class VotacionUsersModel extends Model {

    protected static $table="VotacionUsers";

    public static function ReadModelFromPost($idUser){
      $idVotacion = Input::get("idVotacion");
      //$idUser = Input::get("idUser");
      $votacionUsuario = [
        'idVotacion' => $idVotacion,
        'idUser' => $idUser
      ];
      return $votacionUsuario;
    }

    public static function CreateVotacionUser($votacionUsuario){
      try {
          self::create($votacionUsuario);
          $id = SqLiteSequenceModel::GetLastIdentity(self::$table);
          $votacionUsuario['id'] = $id;
          return ["Code" => CodeSuccess, "message" => "El usuario votó con éxito.", "id" => $id];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo registrar la votación del usuario, {$e->getMessage()}."];
      }
    }

  }

?>