<?php

include_once('./models/Votacion.php');
include_once('./models/Codes.php');
include_once('InternalController.php');
include_once('./models/ResultadoVotacionModel.php');

class ResultadosController extends InternalController
{

  public function index()
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();
    $opciones = 0;
    $mensaje = $_GET['mensaje'];
    $respuesta = VotacionModel::GetAllVotacionesActivasEInactivas();
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
        'title' => 'Mi voto - listado de votaciones pendientes',
        'isMain' => false,
        'isVote' => false,
        'isCreateVote' => false,
        'showVotesManteinment' => false,
        'showPendingVotes' => false,
        'showVotesResults' => true,
        'isEditVote' => false,
        'resultsDetails' => false,
        'votacion' => $votacion,
        'opciones' => count($opciones) > 0 ? $opciones : false,
        'MostrarMensaje' => (isset($mensaje) ? ["message" => $mensaje] : false),
        'user' => $this->User
      ]
    );
  }

  public function show($id)
  {
    if (!$this->IsAutenticated())
      return $this->RedirectToLogin();

    $r = VotacionModel::GetVotacionById($id);
    $votacion = $r['votacion'];
    $opciones = $votacion['opciones'];
    $respuesta = ResultadoVotacionModel::getResultados($id);
    $ResultadosTotal = [];

    if ($respuesta["Code"] == CodeSuccess) {
      $resultados = $respuesta["resultados"];

      foreach ($resultados as $resultado) {

        foreach ($opciones as $opcion) {
          $cantidad = 0;
          if ($resultado['idOpcion'] == $opcion['id']) {
            $cantidad++;
          }
          $opc = [
            'opcion' => $opcion['nombre'],
            'cantidad' => $cantidad,
            'rutaImagen' => $opcion['rutaImagen']
          ];
          array_push($ResultadosTotal, $opc);
        }
      }
    } else {
      $resultados = null;
      $mensaje = (!empty($mensaje) ? "{$mensaje}<br>" : "") . "{$respuesta["Code"]} - {$respuesta["message"]}";
    }
    return view(
      'sitioInterno/index',
      [
        'title' => 'Mi voto - resultados de votaciones' . $ResultadosTotal,
        'isMain' => false,
        'isVote' => false,
        'isCreateVote' => false,
        'showVotesManteinment' => false,
        'showPendingVotes' => false,
        'showVotesResults' => false,
        'isEditVote' => false,
        'resultsDetails' => true,
        'resultados' => $ResultadosTotal,
        'MostrarMensaje' => (isset($mensaje) ? ["message" => $mensaje] : false),
        'user' => $this->User
      ]
    );

  }

}
?>