<?php
date_default_timezone_set('UTC');

require_once ROOT_DIR . '/../config/app.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require $_SERVER['DOCUMENT_ROOT'] .'/PHPMailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] .'/PHPMailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] .'/PHPMailer/src/SMTP.php';

define('SMARTY_DIR', $_SERVER['DOCUMENT_ROOT'] .'/libs/');


function send_activation_email(array $data, string $activation_code): void
{
    // create the activation link
    $activation_link = APP_URL . "/activate.php?email={$data['email']}&activation_code=$activation_code";
    $username = $data['username'];

    // set email subject & body
    $subject = 'Please activate your account';
    $message = <<<MESSAGE
            Hi, $username
            Please click the following link to activate your account:
            $activation_link
            MESSAGE;
    $message = get_message_text('register.tpl', ['username'=>$data['username'], 'link'=>$activation_link]);
    // email header
    $header = "From:" . SENDER_EMAIL_ADDRESS;

    // send the email
    //mail($user['email'], $subject, $message, $header);
}

function send_validation_email(array $data, string $validation_code)
{
    // create the activation link
    $validation_link = APP_URL . "/resetpwd.php?validation_code=$validation_code";
    $username = $data['username'];

    // set email subject & body
    $subject = 'Request for reset password';
    $message = <<<MESSAGE
            Hi, $username
            Please click the following link to reset your account's password:
            $validation_link
            MESSAGE;
    $message = get_message_text('recover.tpl', ['username'=>$username, 'link'=>$validation_link]);
    // email header
    $header = "From:" . SENDER_EMAIL_ADDRESS;

    // send the email
    //mail($user['email'], $subject, $message, $header);

    // log to a file
    //file_put_contents('activation_email.log', $message, FILE_APPEND);
}


function get_message_text(string $template_name, array $data): string
{
    require SMARTY_DIR . 'Smarty.class.php';
    $smarty = new Smarty;
    $smarty->debugging = false;
    $smarty->caching = false;
    $smarty->cache_lifetime = 300;
    $smarty->template_dir = SMARTY_DIR .'../templates/';
    $smarty->compile_dir = SMARTY_DIR .'../templates_c/';

    $smarty->assign("USERNAME", $data['username']);
    $smarty->assign("URL", $data['link']);

    return $smarty->fetch($template_name);
}

function send_confirm_message(int $uuid, array $fields): bool
{
    $mail = new PHPMailer(true);
    //Server settings
    $mail->isSMTP();                         // Send using SMTP
    $mail->SMTPAuth   = true;                // Enable SMTP authentication
    $mail->Host       = MAIL_HOST;           // Set the SMTP server to send through
    $mail->Username   = MAIL_USER;           // SMTP username
    $mail->Password   = MAIL_PASSWORD;       // SMTP password
    $mail->SMTPSecure = MAIL_SMTPSecure;     // Enable TLS encryption
    $mail->Port       = MAIL_PORT; 

    //Recipients
    $mail->setFrom('no-replay@optimus.dp.ua', 'No Replay');     // from who? 
    $mail->addAddress($fields['email'], $fields['fullname']);   // Add a recipient
    //Content
    $mail->isHTML(true);                                        // Set email format to HTML
    $mail->Subject = $fields['subject'];
    $mail->msgHTML($fields['message']);
    try {
      $mail->send();
      //показать на экране дальнейшие шаги.
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }  
}



?>