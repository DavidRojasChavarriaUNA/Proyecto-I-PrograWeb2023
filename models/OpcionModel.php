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
            'idVotacion' => $IdVotacion
        ];
        return $opcion;
    }

    public static function ReadModelFromPost($index){
      $id = Input::get("id{$index}");
      $nombre = Input::get("nombre{$index}");
      $descripcion = Input::get("descripcion{$index}");
      $rutaImagen = Input::get("rutaImagen{$index}");
      $idVotacion = Input::get("idVotacion{$index}");
      $opcion = [
        'id'=>$id, 
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'rutaImagen' => $rutaImagen,
        'idVotacion' => $idVotacion
      ];
      return $opcion;
    }

    public static function CreateOpcion($opcion){
      try {
          $Opciones = $votacion['opciones'];
          //se quitan los campos de control que no son propios de la tabla o que tienen un valor temporal
          unset($opcion['id']);
          unset($opcion['posicion']);

          self::create($opcion);
          $id = SqLiteSequenceModel::GetLastIdentity(self::$table);
          $opcion['id'] = $id;
          return ["Code" => CodeSuccess, "message" => "Opción creada con éxito.", "id" => $id];
      }
      catch (Exception $e) {
          return ["Code" => CodeError, "message" => "No se pudo crear la opción, {$e->getMessage()}."];
      }
    }

  }
  
?>
