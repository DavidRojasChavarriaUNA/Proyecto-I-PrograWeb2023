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
          return ["Code" => CodeSuccess, "message" => "Votación creada con éxito.", "id" => $identity];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo crear la votación, {$e->getMessage()}."];
      }
    }

    public static function RemoveOpcionById($votacion, $idOpcion){
      $Opciones = $votacion['opciones'];
      $OpcionesRestantes = [];
      for($i=0; $i<count($Opciones); $i++){
        $Opcion = $Opciones[$i];
        if($idOpcion != $Opcion['id']){
          array_push($OpcionesRestantes, $Opcion);
        }
      }
      for($x=0; $x<count($OpcionesRestantes); $x++){
        $OpcionesRestantes[$x]['posicion'] = $x;
      }
      unset($Opciones);
      $votacion['totalOpciones'] = count($OpcionesRestantes);
      $votacion['opciones'] = $OpcionesRestantes;
      return $votacion;
    }

    public static function GetVotacionById($id){
      try{
        $votaciones = self::find($id);
        $votacion = null;
        if(!empty($votaciones)){
          $votacion = $votaciones[0];
          $Opciones = OpcionModel::GetOpcionesByIdVotacion($id);
          $votacion['opciones'] = [];
          if(!empty($Opciones)){
            for($i = 0; $i<count($Opciones); $i++){
              $opcion = $Opciones[$i];
              $opcion['posicion'] = $i;
              array_push($votacion['opciones'], $opcion);
            }
          }
          $votacion['totalOpciones'] = count($votacion['opciones']);
          return ["Code" => CodeSuccess, "message" => "Votación obtenida con éxito.", "votacion" => $votacion];
        }
        return ["Code" => CodeNotFound, "message" => "No se encontró una votación con el id: {$id}"];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo obtener la votación, {$e->getMessage()}."];
      }
    }
    
    public static function GetAllVotaciones()
    {
      $votaciones = self::all();
      if(!empty($votaciones)){
        return ["Code" => CodeSuccess, "message" => "Votaciones encontradas", "votacion" => $votaciones];
      }
      return ["Code" => CodeError, "message" => "No cuenta con votaciones registradas"];
    }
  }

?>
