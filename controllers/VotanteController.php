<?php

  include_once('./models/Votacion.php');
  include_once('./models/Codes.php');
  include_once('InternalController.php');

  class VotanteController extends InternalController {      

    public function index() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();
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
            'votacion' => $votacion,
            'opciones' => false,
            'MostrarMensaje' => (isset($mensaje)? ["message" => $mensaje] : false),
            'user'=> $this->User]);
      }

      public function votar($idVotacion) {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();

        $respuesta = VotacionModel::GetVotacionById($idVotacion);
        if ($respuesta["Code"] == CodeSuccess) {
          $votacion = $respuesta["votacion"];
          $opciones = $votacion['opciones'];
        }
        else{
          $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
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
            'votacion' => $votacion,
            'opciones' => count($opciones)>0 ? $opciones : false,
            'MostrarMensaje' => (isset($mensaje)? ["message" => $mensaje] : false),
            'opcionSeleccionada' => false,
            'user'=> $this->User]);
      }

  }
?>