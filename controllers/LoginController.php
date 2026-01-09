<?php

namespace Controllers;

use Classes\email;
use Model\Usuario;
use MVC\Router;

class LoginController{

   # Login
   public static function login(Router $router){
      $alertas = [];

      if($_SERVER['REQUEST_METHOD'] === 'POST'){
         $auth = new Usuario($_POST);

         $alertas = $auth->validarLogin();

         if(empty($alertas)){
            // Comprobar que exista el usuario
            $usuario = Usuario::where('email', $auth->email);

            if($usuario){
               // Verificar el password
               if($usuario->comprobarPasswordAndVerificado($auth->password)){

                  // Autenticar el usuario
                  $_SESSION['id'] = $usuario->id;
                  $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                  $_SESSION['email'] = $usuario->email;
                  $_SESSION['login'] = true;

                  // Redireccionamiento

                  if($usuario->admin === "1"){
                     $_SESSION['admin'] = $usuario->admin ?? null;

                     header('Location: /admin');
                  }else{
                     header('Location: /cita');
                  }
               }
            } 
            
            else {
               Usuario::setAlerta('error', 'Usuario con este correo no existe');
            }
         }
         
      }

      $alertas = Usuario::getAlertas();

      $router->render('auth/login', [
         'alertas' => $alertas
      ]);

    $router->render('auth/login');
   }

   public static function logout(){
    # session_start();
    $_SESSION = [];
    header('Location:/');
   }

   # Contraseña
   public static function olvide(Router $router){

    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $auth = new Usuario($_POST);
      $alertas = $auth->validarEmail();

      if(empty($alertas)){
         $usuario = Usuario::where('email', $auth->email);

         if($usuario && $usuario->confirmado === "1"){
            
            // Generar un token
            $usuario->crearToken();
            $usuario->guardar();

            // Enviar el email
            $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
            $email->enviarIntrucciones();

            // Alerta
            Usuario::setAlerta('exito', 'Te hemos enviado las intrucciones a tu correo');

         }
         else{
            Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
         }
      }
    }

     $alertas = Usuario::getAlertas();

    $router->render('auth/olvide-password', [
      'alertas' => $alertas
    ]);
   }

   public static function recuperar(Router $router){

      $alertas = [];
      $error = false;

      $token = s($_GET['token']);

      // Buscar usuario por token
      $usuario = Usuario::where('token', $token);

      if(empty($usuario)){
         Usuario::setAlerta('error', 'Token no válido');
         $error = true;
      }

      if($_SERVER['REQUEST_METHOD'] === 'POST'){

         // Leer el nuevo password y guardarlo
         $password = new Usuario($_POST);
         $alertas = $password->validarPassword();

         if(empty($alertas)){
            $usuario->password = null;

            $usuario->password = $password->password;
            $usuario->hashPassword();
            $usuario->token = null;

            $resultado = $usuario->guardar();
            if($resultado){
               header('Location: /');
            }
         }
      }

      $alertas = Usuario::getAlertas();
      $router->render('auth/recuperar-password', [
           'alertas' => $alertas,
           'error' => $error
      ]);
   }

   # Cuenta
   public static function crear(Router $router){

      $usuario = new Usuario($_POST); # se va a args del constructor

    // Alertas vacias
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      
        $usuario->sincronizar($_POST); # retornar valores introducidos al validar mal
        $alertas = $usuario->validarNuevaCuenta();

        // Revisar que alertas este vacio
        if(empty($alertas)){
            // Verificar que el usuario no este registrado aun
            $resultado = $usuario->existeUsuario();

            if($resultado->num_rows){
               $alertas = Usuario::getAlertas();
            } else {
               // Hashear el password
               $usuario->hashPassword();

               // Generar un token unico
               $usuario->crearToken();

               // Enviar el email
               $email = new Email($usuario->nombre, $usuario->email,
               $usuario->token);

               $email->enviarConfirmacion();

               // Crea el usuario
               $resultado = $usuario->guardar();
               if($resultado){
                  header('Location: /mensaje');
               }

               #debuguear($usuario);
               
            }
        }
    }

    $router->render('auth/crear-cuenta', [
      'usuario' => $usuario,
      'alertas' => $alertas
    ]);
   }

   public static function mensaje(Router $router){

      $router->render('auth/mensaje');
   }

   public static function confirmar(Router $router){
      $alertas = [];

      $token = s($_GET['token']);

      $usuario = Usuario::where('token', $token);

      if(empty($usuario)){
         // Mostrar error
         Usuario::setAlerta('error', 'Token no válido');
      }else{
         // Confirmado
         $usuario->confirmado = "1";
         $usuario->token = null;
         $usuario->guardar();
         Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
      }
       
      // obtener alertas
      $alertas = Usuario::getAlertas();
      
      // Renderizar la vista
      $router->render('auth/confirmar-cuenta', [
         'alertas' => $alertas
      ]);
   }
}