<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';


function send($body, $file, $Licence)
{
  $err="";
  // отправка письма
  $mail = new PHPMailer(true);
  $mail->CharSet = "utf-8";
  $mail->isHTML(true);
  $mail->setFrom('webmaster@skydive.dp.ua', 'Skydive.dp.ua');
  $mail->addAddress($Licence['Email'], $Licence['Owner']);
  $mail->addBCC('zeroturtle@ua.fm', '');
  $mail->addReplyTo('no-replyto@skydive.dp.ua', 'Noreply');
  $mail->Subject = 'Thank You for subscribe OPTIMUS';
  $mail->AddAttachment($file);
  $mail->msgHTML($body);
  if (!$mail->send()) {
    $err.="Mailer Error: ".$mail->ErrorInfo;
  } else {
    $err.="Message sent!";
  }
  $mail->ClearAllRecipients(); // reset the `To:` list to empty
  return $err;
}



?>