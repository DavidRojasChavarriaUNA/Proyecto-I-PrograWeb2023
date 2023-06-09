<?php

include_once('./models/UserModel.php');
include_once('./models/Codes.php');

class  SeguridadController extends Controller{
    public function showLoginForm() {
        $mensaje = $_GET['mensaje'];
        $error = false;
        if(!isset($mensaje)){
            $mensaje = "";
        }else{
            $error = true;
        }
        return view('seguridad/login', 
        ['title'=>'Mi voto app - Autenticarse',
         'error'=>$error,
         'messaje'=>$mensaje,
         'model' =>['email' => "",'password' => ""]]);
     }

     public function showRegistrationForm() {
        return view('seguridad/register', 
                 ['title'=>'Mi voto app - Registrarse',
                 'login'=>Auth::check()]);
    }
    public function register() {
        $name = Input::get('name');  
        $email = Input::get('email');  
        $user = Input::get('user');  
        $password = Input::get('password');
        $item = [
          'name'=>$name,'email'=>$email,  
          'user'=>$user,'password'=>$password];
        
        $respuesta = UserModel::CreateUser($item);
        if ($respuesta["Code"] == CodeSuccess) {
          return redirect("/seguridad/showLoginForm");
        }
        $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
        return redirect("/seguridad/registrationError?email={$email}&mensaje={$mensaje}");
      }

      public function registrationError() {
        $name = $_GET['name']; 
        $email = $_GET['email'];
        $user = $_GET['user'];   
        $mensaje = $_GET['mensaje'];
        return view('seguridad/register',
          ['title'=>'Mi voto app - Registrarse', 
          'error'=>true,
          'messaje'=>$mensaje,
          'model' =>['email' => $email,'password' => ""]]);
    }
    
    public function autenticate() {
        $email = Input::get('email');   
        $password = Input::get('password');
        $userToAutenticate = ['email' => $email,'password' => $password];
        $respuesta = UserModel::Autenticate($userToAutenticate);
        if ($respuesta["Code"] == CodeSuccess) {
          return redirect("/sitioInterno");
        }
        $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
        return redirect("/seguridad/loginNotAuthorized?email={$email}&mensaje={$mensaje}");
    }

    public function loginNotAuthorized() {
        $email = $_GET['email'];  
        $mensaje = $_GET['mensaje'];
        return view('seguridad/login',
          ['title'=>'Mi voto - Autenticarse', 
          'error'=>true,
          'messaje'=>$mensaje,
          'model' =>['email' => $email,'password' => ""]]);
    }

    public function logout() {
        $respuesta = UserModel::logout();
        if ($respuesta["Code"] == CodeSuccess) {
            return redirect('/');
        }
        else{
            $mensaje = "{$respuesta["Code"]} - {$respuesta["message"]}";
            echo "<h1>{$mensaje}</h1>";
        }
    }

}

?>