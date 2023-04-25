<?php

  class SqLiteSequenceModel extends Model {

    protected static $table="sqlite_sequence";

    public static function GetLastIdentity($tableName){
      $identity = DB::table(Self::$table)->where('name', $tableName)->first();
      return $identity[0]['seq'];
    }

  }
  
?>