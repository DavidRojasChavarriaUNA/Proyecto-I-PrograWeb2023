<?php

class  SeguridadController extends Controller{
    public function login() {
        return view('seguridad/login', 
        ['title'=>'Mi voto app - Autenticarse']);
     }

     public function register() {
         return view('seguridad/register', 
         ['title'=>'Mi voto app - Registrarse']);
      }
}

?>