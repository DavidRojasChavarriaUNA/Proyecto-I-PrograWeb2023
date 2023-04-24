<?php

  include_once('./models/Votacion.php');
  include_once('./models/Codes.php');
  include_once('InternalController.php');

  class VotacionController extends InternalController {  

      

      public function create() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();
        
        $GetFromSession = $_GET['GetFromSession'];
        $votacion = null;
        if(isset($GetFromSession)) 
          $votacion = Session::get(votacion);
        else
          Session::forget(votacion);
        if(!isset($votacion))
          $votacion = VotacionModel::GenerateDefaultVotacion();
        $opciones = $votacion['opciones'];
        
        return view('sitioInterno/index', 
        ['title'=>'Mi voto - crear nueva votación',
         'isVote' => false,
         'isCreateVote' => true,
         'showVotesManteinment' => false,
         'isEditVote' => false,
         'votacion' => $votacion,
         'opciones' =>  $opciones,
         'user'=> $this->User]);
      }

      public function newOption() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();
        $votacion = VotacionModel::ReadModelFromPost();
        $votacion = VotacionModel::AddNewDefaultOption($votacion);
        Session::put(votacion,$votacion);
        $destiny = $_GET['destiny']; 
        if($destiny == "create")
          return redirect(votacionCreate."?GetFromSession=1");
      }

  }
?>