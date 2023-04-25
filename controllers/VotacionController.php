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
         'isMain' => false,
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

        //Util::ReadFile('my_file');
        //$file = Input::file('my_file');
        //echo json_encode($file);

        $votacion = VotacionModel::ReadModelFromPost();
        //echo json_encode($votacion);
        $votacion = VotacionModel::AddNewDefaultOption($votacion);
        Session::put(votacion,$votacion);
        $destiny = $_GET['destiny']; 
        if($destiny == "create")
          return redirect(votacionCreate."?GetFromSession=1");
      }

      public function store() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();

        $votacion = VotacionModel::ReadModelFromPost();
        $respuesta = VotacionModel::CreateVotacion($votacion);

        $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
        if ($respuesta["Code"] == CodeSuccess) {
          Session::forget(votacion);
          return redirect(sprintf(votacionEdit, $respuesta['id']));
        }
        else{
          Session::put(votacion,$votacion);
          return redirect(votacionCreate."?GetFromSession=1");
        }
      }

      public function edit($id) {
        $votacion = null;
        $opciones = null;
        return view('sitioInterno/index', 
        ['title'=>'Mi voto - modificar votación',
         'isMain' => false,
         'isVote' => false,
         'isCreateVote' => false,
         'showVotesManteinment' => false,
         'isEditVote' => true,
         'votacion' => $votacion,
         'opciones' =>  $opciones,
         'user'=> $this->User]);
      }  

  }
?>