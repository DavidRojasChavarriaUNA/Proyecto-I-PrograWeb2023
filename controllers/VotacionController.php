<?php

include_once('./models/Votacion.php');
include_once('./models/Codes.php');
include_once('InternalController.php');

class VotacionController extends InternalController
{

  public function create()
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();

    $GetFromSession = $_GET['GetFromSession'];
    $mensaje = $_GET['mensaje'];

    $votacion = null;
    if (isset($GetFromSession))
      $votacion = Session::get(votacion);
    else
      Session::forget(votacion);
    if (!isset($votacion))
      $votacion = VotacionModel::GenerateDefaultVotacion();
    $opciones = $votacion['opciones'];

    return view(
      'sitioInterno/index',
      [
        'title' => 'Mi voto - crear nueva votación',
        'isMain' => false,
        'isVote' => false,
        'isCreateVote' => true,
        'showVotesManteinment' => false,
        'showPendingVotes' => false,
        'showVotesResults' => false,
        'isEditVote' => false,
        'resultsDetails' => false,
        'votacion' => $votacion,
        'opciones' => count($opciones) > 0 ? $opciones : false,
        'MostrarMensaje' => (isset($mensaje) ? ["message" => $mensaje] : false),
        'destiny' => destiny . "=" . destinyCreate,
        'user' => $this->User
      ]
    );
  }

  public function newOption()
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();

    $votacion = VotacionModel::ReadModelFromPost();
    $id = $votacion['id'];
    $votacion = VotacionModel::AddNewDefaultOption($votacion);
    Session::put(votacion, $votacion);
    $destiny = $_GET[destiny];
    if ($destiny == destinyCreate)
      return redirect(votacionCreate . "?GetFromSession=1&mensaje=0 - Opción agregada");
    if ($destiny == destinyEdit)
      return redirect(sprintf(votacionEdit, $votacion['id']) . "?GetFromSession=1&mensaje=0 - Opción agregada");
  }

  public function removeOption()
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();
    $idOpcion = $_GET['idOpcion'];
    $votacion = VotacionModel::ReadModelFromPost();
    $votacion = VotacionModel::RemoveOpcionById($votacion, $idOpcion);
    Session::put(votacion, $votacion);

    $destiny = $_GET[destiny];
    if ($destiny == destinyCreate)
      return redirect(votacionCreate . "?GetFromSession=1&mensaje=0 - Opción eliminada");
    if ($destiny == destinyEdit)
      return redirect(sprintf(votacionEdit, $votacion['id']) . "?GetFromSession=1&mensaje=0 - Opción eliminada");
  }

  public function store()
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();

    $votacion = VotacionModel::ReadModelFromPost();
    $respuesta = VotacionModel::CreateVotacion($votacion);

    $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
    if ($respuesta["Code"] == CodeSuccess) {
      Session::forget(votacion);
      return redirect(sprintf(votacionEdit, $respuesta['id']) . "?mensaje={$mensaje}");
    } else {
      Session::put(votacion, $votacion);
      return redirect(votacionCreate . "?GetFromSession=1&code={$respuesta["Code"]}&mensaje={$mensaje}");
    }
  }

  public function index()
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();
    Session::forget(votacion);
    $opciones = 0;
    $mensaje = $_GET['mensaje'];
    $respuesta = VotacionModel::GetAllVotaciones();
    if ($respuesta["Code"] == CodeSuccess) {
      $votacion = $respuesta["votacion"];
    } else {
      $votacion = false;
      //si hay un mensaje en $_GET['mensaje'] se concatena al nuevo
      $mensaje = (!empty($mensaje) ? "{$mensaje}<br>" : "") . "{$respuesta["Code"]} - {$respuesta["message"]}";
    }

    return view(
      'sitioInterno/index',
      [
        'title' => 'Mi voto - listado de votaciones',
        'isMain' => false,
        'isVote' => false,
        'isCreateVote' => false,
        'showVotesManteinment' => true,
        'showPendingVotes' => false,
        'showVotesResults' => false,
        'isEditVote' => false,
        'resultsDetails' => false,
        'votacion' => $votacion,
        'opciones' => count($opciones) > 0 ? $opciones : false,
        'MostrarMensaje' => (isset($mensaje) ? ["message" => $mensaje] : false),
        'user' => $this->User
      ]
    );
  }

  public function edit($id)
  {
    if (!$this->IsAutenticated()) return $this->RedirectToLogin();
  
    $GetFromSession = $_GET['GetFromSession'];
    $mensaje = $_GET['mensaje'];
  
    $votacion = null;
    if (isset($GetFromSession))
      $votacion = Session::get(votacion);
    else
      Session::forget(votacion);
      
    if (!isset($votacion)){
      $respuesta = VotacionModel::GetVotacionById($id);
      if ($respuesta["Code"] == CodeSuccess) {
        $votacion = $respuesta["votacion"];
        $opciones = $votacion['opciones'];
      } 
      else {
        //si hay un mensaje en $_GET['mensaje'] se concatena al nuevo
        $mensaje = (!empty($mensaje) ? "{$mensaje}<br>" : "") . "{$respuesta["Code"]} - {$respuesta["message"]}";
        return redirect(votacionIndex . "?mensaje={$mensaje}");
      }
    }

    return view(
      'sitioInterno/index',
      [
        'title' => 'Mi voto - modificar votación',
        'isMain' => false,
        'isVote' => false,
        'isCreateVote' => false,
        'showVotesManteinment' => false,
        'showPendingVotes' => false,
        'showVotesResults' => false,
        'isEditVote' => true,
        'resultsDetails' => false,
        'votacion' => $votacion,
        'opciones' => count($opciones) > 0 ? $opciones : false,
        'MostrarMensaje' => (isset($mensaje) ? ["message" => $mensaje] : false),
        'destiny' => destiny . "=" . destinyEdit,
        'user' => $this->User
      ]
    );
  }

  public function update($_, $id)
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();

    $votacion = VotacionModel::ReadModelFromPost();
    $respuesta = VotacionModel::UpdateVotacion($votacion);
    $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
    if ($respuesta["Code"] == CodeSuccess) {
      Session::forget(votacion);
      return redirect(votacionIndex . "?GetFromSession=1&code={$respuesta["Code"]}&mensaje={$mensaje}");
    } else {
      Session::put(votacion, $votacion);
      return redirect(sprintf(votacionEdit, $respuesta['id']) . "?mensaje={$mensaje}");
    }
  }
  public function destroy($id)
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();

    $respuesta = VotacionModel::DestroyVotacionWithOpciones($id);
    $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
    return redirect(votacionIndex . "?mensaje={$mensaje}");
  }

  public function cambiarEstado($id)
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();

    $respuesta = VotacionModel::GetVotacionById($id);
    if ($respuesta["Code"] == CodeSuccess) {
      $votacion = $respuesta["votacion"];
      $descripcion = $votacion['descripcion'];
      $fechaHoraInicio = $votacion['fechaHoraInicio'];
      $fechaHoraFin = $votacion['fechaHoraFin'];
      $item = [
        'id' => $id,
        'descripcion' => $descripcion,
        'idEstado' => 3,
        'fechaHoraInicio' => $fechaHoraInicio,
        'fechaHoraFin' => $fechaHoraFin
      ];
      $respuesta = VotacionModel::ChangeState($item);
      $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
    } else {
      $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
    }
    return redirect(votacionIndex . "?mensaje={$mensaje}");
  }

}
?>