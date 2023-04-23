<?php

include_once('./models/UserModel.php');
include_once('./models/Codes.php');

class  SeguridadController extends Controller{
    public function showLoginForm() {
        return view('seguridad/login', 
        ['title'=>'Mi voto app - Autenticarse',
         'error'=>false,
         'messaje'=>"",
         'model' =>['email' => "",'password' => ""]]);
     }

     public function showRegistrationForm() {
         return view('seguridad/register', 
         ['title'=>'Mi voto app - Registrarse']);
    }
    public function register() {
        $name = Input::get('name');  
        $email = Input::get('email');  
        $user = Input::get('user');  
        $password = Input::get('password');
        $userCreate = [
          'name'=>$name,'email'=>$email,  
          'user'=>$user,'password'=>$password];
        UserModel::create($userCreate);
        return redirect('/');
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
          ['title'=>'Mi voto app - Autenticarse', 
          'error'=>true,
          'messaje'=>$mensaje,
          'model' =>['email' => $email,'password' => ""]]);
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }

}

?>