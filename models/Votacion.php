<?php

  include_once('Codes.php');
  include_once('Util.php');
  include_once('OpcionModel.php');
  include_once('SqLiteSequenceModel.php');
  
  class VotacionModel extends Model {

    protected static $table="Votacion";

    public static function GenerateDefaultVotacion(){
      //generates a uniqued integer id for temporaly option
      $id = Util::GenerateUniqueId();
      $Opciones = [];
      for($i = 0; $i<OpcionesPorDefecto; $i++){
        $opcion = OpcionModel::GenerateDefaultOption($id);
        $opcion['posicion'] = $i;
        array_push($Opciones, $opcion);
      }
      $votacion = [
          'id'=>$id, 
          'descripcion' => "",
          'idEstado' => EstadoEnProceso,
          'fechaHoraInicio' => "",
          'fechaHoraFin' => "",
          'opciones' => $Opciones,
          'totalOpciones' => OpcionesPorDefecto
      ];
      return $votacion;
    }

    public static function ReadModelFromPost(){
      $id = Input::get('id');
      $descripcion = Input::get('descripcion');
      $idEstado = Input::get('idEstado');
      $fechaHoraInicio = Input::get('fechaHoraInicio');
      $fechaHoraFin = Input::get('fechaHoraFin');
      $totalOpciones = Input::get('totalOpciones');
      $votacion = [
        'id'=>$id,
        'descripcion'=>$descripcion,
        'idEstado'=>$idEstado,
        'fechaHoraInicio'=>$fechaHoraInicio,
        'fechaHoraFin'=>$fechaHoraFin,
        'totalOpciones' =>$totalOpciones
      ];
      $Opciones = [];
      for($i = 0; $i<$votacion['totalOpciones']; $i++){
        $opcion = OpcionModel::ReadModelFromPost($i);
        $opcion['posicion'] = $i;
        array_push($Opciones, $opcion);
      }
      $votacion['opciones'] = $Opciones;
      return $votacion;
    }  
    
    public static function AddNewDefaultOption($votacion){
      $opcion = OpcionModel::GenerateDefaultOption($votacion['id']);
      $opcion['posicion'] = $votacion['totalOpciones'];
      array_push($votacion['opciones'], $opcion);
      $votacion['totalOpciones']++;
      return $votacion;
    }

    public static function CreateVotacion($votacion){
      try {
          $Opciones = $votacion['opciones'];
          //se quitan los campos de control que no son propios de la tabla o que tienen un valor temporal
          unset($votacion['id']);
          unset($votacion['opciones']);
          unset($votacion['totalOpciones']);

          self::create($votacion);
          $identity = SqLiteSequenceModel::GetLastIdentity(self::$table);
          $votacion['id'] = $identity;

          foreach($Opciones as $Opcion){
            $Opcion['idVotacion'] = $identity;
            $respuesta = OpcionModel::CreateOpcion($Opcion);
            if ($respuesta["Code"] == CodeSuccess) {
              $Opcion['id'] = $respuesta['id'];
            }
            else{
              $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
              throw new Exception($mensaje);
            }
          }
          return ["Code" => CodeSuccess, "message" => "Votación creado con éxito.", "id" => $identity];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo crear la votación, {$e->getMessage()}."];
      }
    }

  }

?>
