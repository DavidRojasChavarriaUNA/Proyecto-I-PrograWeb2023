<?php

  include_once('Codes.php');
  include_once('Util.php');
  include_once('SqLiteSequenceModel.php');

  class OpcionModel extends Model {
    
    protected static $table="Opcion";

    public static function GenerateDefaultOption($IdVotacion){
        //generates a uniqued integer id for temporaly option
        $id = Util::GenerateUniqueId();
        $opcion = [
            'id'=>$id, 
            'nombre' => "",
            'descripcion' => "",
            'rutaImagen' => RutaImagenDefault,
            'idVotacion' => $IdVotacion,
            'opcionNueva' => Si
        ];
        return $opcion;
    }

    public static function ReadModelFromPost($index){
      $id = Input::get("id{$index}");
      $nombre = Input::get("nombre{$index}");
      $descripcion = Input::get("descripcion{$index}");
      $rutaImagen = Input::get("rutaImagen{$index}");
      $idVotacion = Input::get("idVotacion{$index}");
      $idOpc= Input::get("id{$index}");
      $descripcionOpc = Input::get("descripcion{$index}");
      $opcionNueva = Input::get("opcionNueva{$index}");
      $opcion = [
        'id'=>$id, 
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'rutaImagen' => $rutaImagen,
        'idVotacion' => $idVotacion,
        'idOpc' => $idOpc,
        'descripcionOpc' => $descripcionOpc    ,
        'opcionNueva' => $opcionNueva 
      ];
      return $opcion;
    }

    public static function CreateOpcion($opcion){
      try {
         // $Opciones = $votacion['opciones'];
          //se quitan los campos de control que no son propios de la tabla o que tienen un valor temporal
          unset($opcion['id']);
          unset($opcion['posicion']);
          unset($opcion['idOpc']);
          unset($opcion['descripcionOpc']);
          unset($opcion['opcionNueva']);

          self::create($opcion);
          $id = SqLiteSequenceModel::GetLastIdentity(self::$table);
          $opcion['id'] = $id;
          return ["Code" => CodeSuccess, "message" => "Opción creada con éxito.", "id" => $id];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo crear la opción, {$e->getMessage()}."];
      }
    }

    public static function GetOpcionById($id){
      try{
        $opciones = self::find($id);
        $opcion = null;
        if(!empty($opciones)){
          $opcion = $opciones[0];
          $opcion['opcionNueva'] = No;
          return ["Code" => CodeSuccess, "message" => "Opción obtenida con éxito.", "opcion" => $opcion];
        }
        return ["Code" => CodeNotFound, "message" => "No se encontró una opción con el id: {$id}"];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo obtener la opción, {$e->getMessage()}."];
      }
    }

    public static function GetOpcionesByIdVotacion($idVotacion){
      $opciones = self::where('idVotacion',$idVotacion);
      //$opciones = DB::table(self::$table)->where('idVotacion',$idVotacion)->orderBy('id', 'ASC')->get();
      return $opciones;
    }

    public static function DestroyOpcion($id){
      try {
        self::destroy($id);
        return ["Code" => CodeSuccess, "message" => "Opción borrada con éxito."];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo borrar la opción, {$e->getMessage()}."];
      }
    }

    public static function UpdateOpcion($item){
      try {
        unset($item['posicion']);
        unset($item['idOpc']);
        unset($item['descripcionOpc']);
        unset($item['opcionNueva']);
        $id = $item['descripcion'];
          self::update($item['id'], $item);
          return ["Code" => CodeSuccess, "message" => "Opcion modificado con éxito. $id"];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo modificar la opcion, {$e->getMessage()}."];
      }
    }

  }
  
?>
