<?php

  include_once('./models/Votacion.php');
  include_once('./models/Codes.php');
  include_once('InternalController.php');

  class VotacionController extends InternalController {  

      public function create() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();
        return view('sitioInterno/index', 
        ['title'=>'Mi voto - crear nueva votación',
         'isVote' => false,
         'isCreateVote' => true,
         'showVotesManteinment' => false,
         'isEditVote' => false,
         'user'=> $this->User]);
      }

  }
?>