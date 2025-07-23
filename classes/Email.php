<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email,$nombre,$token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion(){

        // Looking to send emails in production? Check out our Email API/SMTP product!
        // Looking to send emails in production? Check out our Email API/SMTP product!
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '65dd880795c106';
        $mail->Password = '52cf786c68ee56';
        $mail->setFrom('victorvelizluna@gmail.com');
        $mail->addAddress('victorvelizluna@gmail.com', 'gmail.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p>Hola<strong> " . $this->nombre . "</strong> has creado tu cuenta en Constructora, confirmala en el siguiente enlace.</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:8000/confirm?token=" . 
        $this->token ."'>Confirmar cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje.</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;


        //Enviar el email
        $mail->send();
    }

    public function enviarInstrucciones(){

        // Looking to send emails in production? Check out our Email API/SMTP product!
        // Looking to send emails in production? Check out our Email API/SMTP product!
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '65dd880795c106';
        $mail->Password = '52cf786c68ee56';
        $mail->setFrom('victorvelizluna@gmail.com');
        $mail->addAddress('victorvelizluna@gmail.com', 'gmail.com');
        $mail->Subject = 'Restablece tu contraseña';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p>Hola<strong> " . $this->nombre . "</strong> selecciona el siguiente enlace para recuperar tu contraseña.</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:8000/recover?token=" . 
        $this->token ."'>Restablecer contraseña</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje.</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;


        //Enviar el email
        $mail->send();
    }
}