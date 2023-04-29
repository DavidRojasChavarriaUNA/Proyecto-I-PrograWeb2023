<?php

  include_once('./models/Votacion.php');
  include_once('./models/OpcionModel.php');
  include_once('./models/Codes.php');
  include_once('./models/ResultadoVotacionModel.php');
  include_once('./models/VotacionUsersModel.php');
  include_once('InternalController.php');

  class VotanteController extends InternalController {      

    public function index() {
      if(!$this->IsAutenticated()) return $this->RedirectToLogin();

      Session::forget(opcionSeleccionada);

      $mensaje = $_GET['mensaje'];
      $respuesta = VotacionModel::GetAllVotacionesPendientesVotarUser($this->User['id']);
      if ($respuesta["Code"] == CodeSuccess) {
        $votacion = $respuesta["votacion"];
      }
      else{
        $votacion=false;
        //si hay un mensaje en $_GET['mensaje'] se concatena al nuevo
        $mensaje = (!empty($mensaje)? "{$mensaje}<br>" : "") . "{$respuesta["Code"]} - {$respuesta["message"]}";
      }

      return view('sitioInterno/index', 
        ['title' => 'Mi voto - listado de votaciones pendientes',
        'isMain' => false,
        'isVote' => false,
        'isCreateVote' => false,
        'showVotesManteinment' => false,
        'showPendingVotes' => true,
        'showVotesResults' => false,
        'isEditVote' => false,
        'resultsDetails' => false,
        'votacion' => $votacion,
        'opciones' => false,
        'MostrarMensaje' => (isset($mensaje)? ["message" => $mensaje] : false),
        'user'=> $this->User]);
    }

    public function votar($idVotacion) {
      if(!$this->IsAutenticated()) return $this->RedirectToLogin();
      $mensaje = $_GET['mensaje'];
      
      $opcionSeleccionada = Session::get(opcionSeleccionada);

      $idOpcionSeleccionada = null;
      $OpcionConfirmar = null;

      if(isset($opcionSeleccionada)){
        $idOpcionSeleccionada = $opcionSeleccionada['idOpcion'];
        $respuesta = OpcionModel::GetOpcionById($idOpcionSeleccionada);
        if ($respuesta["Code"] == CodeSuccess) {
          $OpcionConfirmar = $respuesta["opcion"];
        }
        else{
          //si hay un mensaje en $_GET['mensaje'] se concatena al nuevo
          $mensaje = (!empty($mensaje)? "{$mensaje}<br>" : "") . "{$respuesta["Code"]} - {$respuesta["message"]}";
          //rediereccionar al index y mostrar error
          return redirect(votanteIndex."?mensaje={$mensaje}");
        }
      }
      
      $respuesta = VotacionModel::GetVotacionById($idVotacion, $idOpcionSeleccionada);
      if ($respuesta["Code"] == CodeSuccess) {
        $votacion = $respuesta["votacion"];
        $opciones = $votacion['opciones'];
      }
      else{
        //si hay un mensaje en $_GET['mensaje'] se concatena al nuevo
        $mensaje = (!empty($mensaje)? "{$mensaje}<br>" : "") . "{$respuesta["Code"]} - {$respuesta["message"]}";
        //rediereccionar al index y mostrar error
        return redirect(votanteIndex."?mensaje={$mensaje}");
      }

      return view('sitioInterno/index', 
        ['title' => 'Mi voto - listado de votaciones pendientes',
        'isMain' => false,
        'isVote' => true,
        'isCreateVote' => false,
        'showVotesManteinment' => false,
        'showPendingVotes' => false,
        'showVotesResults' => false,
        'isEditVote' => false,
        'resultsDetails' => false,
        'votacion' => $votacion,
        'opciones' => count($opciones)>0 ? $opciones : false,
        'MostrarMensaje' => (isset($mensaje)? ["message" => $mensaje] : false),
        'opcionSeleccionada' => (isset($opcionSeleccionada)? true: false),
        'opcionConfirmar' => (isset($OpcionConfirmar)? $OpcionConfirmar: false),
        'user'=> $this->User]);
    }

    public function chooseOption() {
      if(!$this->IsAutenticated()) return $this->RedirectToLogin();

      $opcionSeleccionada = ResultadoVotacionModel::ReadModelFromPost();
      Session::put(opcionSeleccionada, $opcionSeleccionada);
      return redirect(sprintf(votanteVotar, $opcionSeleccionada['idVotacion']));
    }

    public function confirmOptionVote() {
      if(!$this->IsAutenticated()) return $this->RedirectToLogin();

      $opcionSeleccionada = ResultadoVotacionModel::ReadModelFromPost();

      Session::forget(opcionSeleccionada);

      $respuesta = ResultadoVotacionModel::CreateResultadoVotacion($opcionSeleccionada);
      $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
      if ($respuesta["Code"] == CodeSuccess) {
        $votacionUsuario = VotacionUsersModel::ReadModelFromPost($this->User['id']);
        $respuesta = VotacionUsersModel::CreateVotacionUser($votacionUsuario);
        //si hay un mensaje anterior se concatena al nuevo
        $mensaje = (!empty($mensaje)? "{$mensaje}<br>" : "") . "{$respuesta["Code"]} - {$respuesta["message"]}";
        echo $mensaje;
      }
      //rediereccionar al index y mostrar mensaje
      return redirect(votanteIndex."?mensaje={$mensaje}");
    }

  }
?>