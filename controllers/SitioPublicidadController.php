<?php

class  SitioPublicidadController extends Controller{
    public function index() {
        return view('sitioPublicidad/index', 
        ['title'=>'Mi voto app']);
     }
}

?>