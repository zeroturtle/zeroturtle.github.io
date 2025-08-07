<?php
date_default_timezone_set('UTC');

require 'libs/Smarty.class.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';


function get_message_text(array $user, string $activation_link): string
{
    $smarty = new Smarty;
    $smarty->debugging = false;
    $smarty->caching = false;
    $smarty->cache_lifetime = 300;
    $smarty->assign("USERNAME", $user['username']);
    $smarty->assign("URL", $activation_link);
    return $smarty->fetch('register.tpl')
}

function send_confirm_message(int $uuid, array $fields): bool
{
    $mail = new PHPMailer(true);
    //Server settings
    $mail->isSMTP();                         // Send using SMTP
    $mail->Host       = MAIL_HOST;           // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                // Enable SMTP authentication
    $mail->Username   = MAIL_USER;           // SMTP username
    $mail->Password   = MAIL_PASSWORD;       // SMTP password
    $mail->SMTPSecure = 'tls';               // Enable TLS encryption
    $mail->Port       = MAIL_PORT; 
    //Recipients
    $mail->setFrom('no-replay@optimus.dp.ua', 'No Replay');     // from who? 
    $mail->addAddress($fields['email'], $fields['fullname']);   // Add a recipient
    //Content
    $mail->isHTML(true);                                        // Set email format to HTML
    $mail->Subject = 'OPTIMUS registration link';
    $mail->msgHTML($message);
    try {
      $mail->send();
      //показать на экране дальнейшие шаги.
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }  
}



?>