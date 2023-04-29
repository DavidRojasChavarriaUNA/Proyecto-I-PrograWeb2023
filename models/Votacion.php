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

    public static function GetVotacionById($id, $idOpcionSeleccionada = null){
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
              $opcion['idOpc'] = $opcion['id'];
              $opcion['descripcionOpc'] = $opcion['descripcion'];
              if(isset($idOpcionSeleccionada) && $opcion['id'] == $idOpcionSeleccionada){
                $opcion['isChecked'] = [true];
              }else{
                $opcion['isChecked'] = [false];
              }
              $opcion['descripcionOpc'] = $opcion['descripcion'];
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
      //se actualiza el estado de las votaciones
      $respuesta = self::AutomaticUpdateStatesVotaciones();
      //si falló se debe mostrar la respuesta
      if ($respuesta["Code"] != CodeSuccess){
        return $respuesta;
      }
      //$votaciones = self::all();   
      //se usa una vista para traer la descripción del estado
      $votaciones = DB::table("vwVotaciones")->get();
      if(!empty($votaciones)){
        return ["Code" => CodeSuccess, "message" => "Votaciones encontradas", "votacion" => $votaciones];
      }
      return ["Code" => CodeError, "message" => "No cuenta con votaciones registradas"];
    }

    public static function GetAllVotacionesPendientesVotarUser($idUser)
    {
      //se actualiza el estado de las votaciones
      $respuesta = self::AutomaticUpdateStatesVotaciones();
      //si falló se debe mostrar la respuesta
      if ($respuesta["Code"] != CodeSuccess){
        return $respuesta;
      }
      //se usa una vista para traer la descripción del estado y las votaciones pendientes de los usuarios
      $votaciones = DB::table("vwVotacionesPendientesUsuario")->where('idUser', $idUser)->get();
      if(!empty($votaciones)){
        return ["Code" => CodeSuccess, "message" => "Votaciones encontradas", "votacion" => $votaciones];
      }
      return ["Code" => CodeError, "message" => "No cuenta con votaciones pendientes"];
    }

    public static function GetAllVotacionesActivasEInactivas()
    {
      //se actualiza el estado de las votaciones
      $respuesta = self::AutomaticUpdateStatesVotaciones();
      //si falló se debe mostrar la respuesta
      if ($respuesta["Code"] != CodeSuccess){
        return $respuesta;
      }
      //$votaciones = self::all();   
      //se usa una vista para traer la descripción del estado
      $votacionesActivas = DB::table("vwVotaciones")->where('idEstado', EstadoActivo)->get();
      $votacionesInactivas = DB::table("vwVotaciones")->where('idEstado', EstadoInactivo)->get();
      $votaciones = array_merge($votacionesActivas, $votacionesInactivas);
      if(!empty($votaciones)){
        return ["Code" => CodeSuccess, "message" => "Votaciones encontradas", "votacion" => $votaciones];
      }
      return ["Code" => CodeError, "message" => "No cuenta con votaciones finalizadas"];
    }

    public static function DestroyVotacion($id){
      $Opciones = OpcionModel::GetOpcionesByIdVotacion($id);
      if(count($Opciones) > 0){
          return ["Code" => CodeError, "message" => "No se puede borrar la votación, existen opciones asociadas a la votación."];
      }
      self::destroy($id);
      return ["Code" => CodeSuccess, "message" => "Votación borrada con éxito."];
    }

    public static function DestroyVotacionWithOpciones($id){
      try{
        $respuesta = VotacionModel::GetVotacionById($id);
        if ($respuesta["Code"] != CodeSuccess)
          return $respuesta;
        $votacion = $respuesta["votacion"];
        //se debe eliminar uno por uno porque no encontré un método para eliminar con where en phpframex.
        foreach($votacion['opciones'] as $Opcion){
          $respuesta = OpcionModel::DestroyOpcion($Opcion['id']);
          if ($respuesta["Code"] != CodeSuccess) {
            $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
            throw new Exception($mensaje);
          }
        }
        $respuesta = self::DestroyVotacion($id);
        return $respuesta;
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo eliminar la votación, {$e->getMessage()}."];
      }
    }
    
    public static function UpdateVotacion($item)
  {
    try {
      $Opciones = $item['opciones'];
      unset($item['opciones']);
      unset($item['totalOpciones']);
      echo $Opciones[0]['nombre']; //

      if (!empty($Opciones)) {
        foreach ($Opciones as $Opcion) {
          $respuesta = OpcionModel::UpdateOpcion($Opcion);
          echo $respuesta['message'];
          if ($respuesta["Code"] == CodeError) {
            $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
            throw new Exception($mensaje);
          }
        }
      }
   
      self::update($item['id'], $item);
      return ["Code" => CodeSuccess, "message" => "Votación modificado con éxito."];
    } catch (Exception $e) {
      return ["Code" => CodeError, "message" => "No se pudo modificar la votación, {$e->getMessage()}."];
    }
  }


    public static function ChangeState($item){
      try {
          self::update($item["id"], $item);
          return ["Code" => CodeSuccess, "message" => "Estado modificado con éxito."];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo modificar el estado, {$e->getMessage()}."];
      }
    }

    public static function UpdateStatusVotacionEnProcesoIfIsRequerid(&$votacion){
      if($votacion['idEstado'] != EnProceso)
        return;

      //si no tiene fecha y hora de inicio se actuva manualmente
      if(empty($votacion['fechaHoraInicio']))
        return;

      $fechaHoraInicioVotacion = DateTime::createFromFormat(FormatoFechaHoraDB, $votacion['fechaHoraInicio'], new DateTimeZone(CostaRicaTimeZone));
      $currenteDateTime = new Datetime('now', new DateTimeZone(CostaRicaTimeZone));

      //si la fecha y hora de inicio es mayor a la actual no debe activarse
      if($fechaHoraInicioVotacion > $currenteDateTime)
        return;

      //caso contrario se activa
      $votacion['idEstado'] = Activo;
      self::update($votacion["id"], $votacion);
    }

    public static function UpdateStatusVotacionActivaIfIsRequerid(&$votacion){
      if($votacion['idEstado'] != Activo)
        return;
      //si no tiene fecha y hora de fin, se desactiva manualnente.
      if(empty($votacion['fechaHoraFin']))
        return $votacion;

      $fechaHoraFinVotacion = DateTime::createFromFormat(FormatoFechaHoraDB, $votacion['fechaHoraFin'], new DateTimeZone(CostaRicaTimeZone));
      $currenteDateTime = new Datetime('now', new DateTimeZone(CostaRicaTimeZone));

      //si la fecha y hora de fin es mayor a la fecha actual no debe desactivarse
      if($fechaHoraFinVotacion > $currenteDateTime)
        return;
        
      //caso contrario se inactiva
      $votacion['idEstado'] = Inactivo;
      self::update($votacion["id"], $votacion);
      return;
    }

    //este proceso debería realizarse en base de datos por medio de un proceso automático como jobs o schedules.
    public static function AutomaticUpdateStatesVotaciones(){
      try{
        $votaciones = self::all();
        if(!isset($votaciones))
          return ["Code" => CodeSuccess, "message" => "No hay votaciones por provesar su estado."];

        foreach($votaciones as $votacion){
          self::UpdateStatusVotacionEnProcesoIfIsRequerid($votacion);
          self::UpdateStatusVotacionActivaIfIsRequerid($votacion);
        }

        return ["Code" => CodeSuccess, "message" => "Se ha procesado el estado de las votaciones con éxito."];
      }catch(Exception $e){
        return ["Code" => CodeError, "message" => "No se pudo y actualizar los estados de forma automática, {$e->getMessage()}."];
      }
    }

  }

?>
