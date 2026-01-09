<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;


class Email{

    public $nombre;
    public $email;
    public $token;

    public function __construct($nombre, $email, $token)
    {
        $this->nombre = $nombre;
        $this->email= $email;
        $this->token = $token;

    }

    public function enviarConfirmacion(){

        // Crear objeto de email
       $mail = new PHPMailer();
       $mail->isSMTP();
       $mail->Host = $_ENV['EMAIL_HOST'];
       $mail->SMTPAuth = true;
       $mail->Port = $_ENV['EMAIL_PORT'];
       $mail->Username = $_ENV['EMAIL_USER'];
       $mail->Password = $_ENV['EMAIL_PASS'];
       
   
        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        // Set html
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>"; 
        $contenido .= "<p> <strong> Hola " . $this->nombre . " </strong> Has creado tu cuenta en AppSalon
        solo debes confirmarla presionando el siguiente enlace </p>";
        $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['APP_URL']  . "/confirmar-cuenta?token=" . $this->token . " '>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;

        // Enviar el email
        $mail->send();

    }

    public function enviarIntrucciones(){

       // Crear objeto de email
       $mail = new PHPMailer();
       $mail->isSMTP();
       $mail->Host = $_ENV['EMAIL_HOST'];
       $mail->SMTPAuth = true;
       $mail->Port = $_ENV['EMAIL_PORT'];
       $mail->Username = $_ENV['EMAIL_USER'];
       $mail->Password = $_ENV['EMAIL_PASS'];
       
   
        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Reestablece tu contraseña';

        // Set html
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>"; 
        $contenido .= "<p> <strong> Hola " . $this->nombre . " </strong> Has solicitado reestablecer tu
        contraseña de AppSalon, continúa el proceso presionando el siguiente enlace </p>";
        $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['APP_URL']  . "/0/recuperar?token=" . $this->token . " '>Reestablece contraseña</a></p>";
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar este mensaje</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;

        // Enviar el email
        $mail->send();
    }
}