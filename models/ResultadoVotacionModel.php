<?php

  include_once('Codes.php');
  include_once('Util.php');
  include_once('SqLiteSequenceModel.php');

  class ResultadoVotacionModel extends Model {

    protected static $table="ResultadoVotacion";

    public static function ReadModelFromPost(){
      $idVotacion = Input::get("idVotacion");
      $idOpcion = Input::get("idOpcion");
      $opcionSeleccionada = [
        'idVotacion' => $idVotacion,
        'idOpcion' => $idOpcion
      ];
      return $opcionSeleccionada;
    }

    public static function CreateResultadoVotacion($resultadoVotacion){
      try {
          self::create($resultadoVotacion);
          $id = SqLiteSequenceModel::GetLastIdentity(self::$table);
          $resultadoVotacion['id'] = $id;
          return ["Code" => CodeSuccess, "message" => "Votación realizada con éxito.", "id" => $id];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo registrar la votación, {$e->getMessage()}."];
      }
    }

    public static function getResultados($idVotacion){
       $resultados = DB::table(Self::$table)->where('idVotacion', $idVotacion)->get();
      if(!empty($resultados)){
        return ["Code" => CodeSuccess, "message" => "Resultados encontradas", "resultados" => $resultados];
      } 
      return ["Code" => CodeError, "message" => "No cuenta con resultados registrados"];
    }
  }

?>