<?php

  include_once('./models/Votacion.php');
  include_once('./models/Codes.php');
  include_once('InternalController.php');

  class VotacionController extends InternalController {

      public function create() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();
        
        $GetFromSession = $_GET['GetFromSession'];
        $mensaje = $_GET['mensaje'];

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
         'opciones' => count($opciones)>0 ? $opciones : false,
         'MostrarMensaje' => (isset($mensaje)? ["message" => $mensaje] : false),
         'destiny' => destiny."=".destinyCreate,
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
        $destiny = $_GET[destiny]; 
        if($destiny == destinyCreate)
          return redirect(votacionCreate."?GetFromSession=1&mensaje=0 - Opción agregada");
      }

      public function removeOption() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();
        $idOpcion = $_GET['idOpcion']; 
        $votacion = VotacionModel::ReadModelFromPost();
        $votacion = VotacionModel::RemoveOpcionById($votacion, $idOpcion);
        Session::put(votacion,$votacion);

        $destiny = $_GET[destiny]; 
        if($destiny == destinyCreate)
          return redirect(votacionCreate."?GetFromSession=1&mensaje=0 - Opción eliminada");
      }

      public function store() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();

        $votacion = VotacionModel::ReadModelFromPost();
        $respuesta = VotacionModel::CreateVotacion($votacion);

        $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
        if ($respuesta["Code"] == CodeSuccess) {
          Session::forget(votacion);
          return redirect(sprintf(votacionEdit, $respuesta['id'])."?mensaje={$mensaje}");
        }
        else{
          Session::put(votacion,$votacion);
          return redirect(votacionCreate."?GetFromSession=1&code={$respuesta["Code"]}&mensaje={$mensaje}");
        }
      }

      public function index() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();
        $opciones = 0;
        $mensaje = $_GET['mensaje'];
        $respuesta = VotacionModel::GetAllVotaciones();
        if ($respuesta["Code"] == CodeSuccess) {
          $votacion = $respuesta["votacion"];
        }
        else{
          $votacion=false;
          $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
        }

        return view('sitioInterno/index', 
            ['title' => 'Mi voto - listado de votaciones',
            'isMain' => false,
            'isVote' => false,
            'isCreateVote' => false,
            'showVotesManteinment' => true,
            'isEditVote' => false,
            'votacion' => $votacion,
            'opciones' => count($opciones)>0 ? $opciones : false,
            'MostrarMensaje' => (isset($mensaje)? ["message" => $mensaje] : false),
            'user'=> $this->User]);
      }

      public function edit($id) {
        $respuesta = VotacionModel::GetVotacionById($id);
        if ($respuesta["Code"] == CodeSuccess) {
          $votacion = $respuesta["votacion"];
          $opciones = $votacion['opciones'];
        }
        else{
          $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
          //rediereccionar al index y mostrar error
          return redirect(votacionIndex."?mensaje={$mensaje}");
        }

        $mensaje = $_GET['mensaje'];

        return view('sitioInterno/index', 
        ['title'=>'Mi voto - modificar votación',
         'isMain' => false,
         'isVote' => false,
         'isCreateVote' => false,
         'showVotesManteinment' => false,
         'isEditVote' => true,
         'votacion' => $votacion,
         'opciones' => count($opciones)>0 ? $opciones : false,
         'MostrarMensaje' => (isset($mensaje)? ["message" => $mensaje] : false),
         'destiny' => destiny."=".destinyEdit,
         'user'=> $this->User]);
      }  

      public function cambiarEstado($id,$idEstado) {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();
        $respuesta = VotacionModel::GetVotacionById($id);
        if ($respuesta["Code"] == CodeSuccess) { 
          $votacion = $respuesta["votacion"];
          $descripcion = $votacion['descripcion'];
          $fechaHoraInicio = $votacion['fechaHoraInicio'];
          $fechaHoraFin = $votacion['fechaHoraFin'];
          $item = ['id'=>$id,
                   'descripcion'=>$descripcion,'idEstado'=>$idEstado,
                   'fechaHoraInicio'=>$fechaHoraInicio,'fechaHoraFin'=>$fechaHoraFin];
          $respuesta = VotacionModel::ChangeState($item);
          $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
        }
        else{
          $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
        }
        return redirect(votacionIndex."?mensaje={$mensaje}");
      }
  }
?>