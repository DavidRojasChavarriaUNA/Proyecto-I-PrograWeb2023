<?php

  include_once('Codes.php');

  class Util {

    public static function GenerateUniqueId(){
        //generates a uniqued integer id for temporaly option
        $id = abs( crc32( uniqid() ) );
        return $id;
    }

    /*public static function ReadFile($name){
      echo "aqui";
      if(!isset($_FILES[$name])){
        echo "file not uploaded {$name}";
        return;
      }
      $uploaddir = './imagenes/uploads/';
      $uploadfile = $uploaddir . basename($_FILES[$name]['name']);
      if (move_uploaded_file($_FILES[$name]['tmp_name'], $uploadfile)) {
          echo "File is valid, and was successfully uploaded.\n";
      } else {
          echo "Possible file upload attack!\n";
      }
    }*/

  }
  
?>