<?php

include_once('./models/UserModel.php');
include_once('./models/Codes.php');

class  InternalController extends Controller{

    private $messaje;
    protected $User;

    private function LoadAutenticatedUser(){
        $currentId = Session::get('id');
        $respuesta = UserModel::getUserById($currentId);
        if ($respuesta["Code"] == CodeSuccess) {
            $this->User = $respuesta["User"];
        }
        else{
            $this->messaje = $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
            $this->User = null;
        }
    }

    protected function IsAutenticated(){
        $respuesta = UserModel::IsAutenticated();
        if ($respuesta["Code"] != CodeSuccess) {
            $this->messaje = $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
            return false;
        }
        else{
            $this->LoadAutenticatedUser();
        }
        return true;
    }

    protected function RedirectToLogin(){
        return redirect("/seguridad/showLoginForm?mensaje={$this->messaje}");
    }

}

?>