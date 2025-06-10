<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require './PHPMailer/src/Exception.php';
  require './PHPMailer/src/PHPMailer.php';
  //require './PHPMailer/src/SMTP.php';
  $mail = new PHPMailer(true);

  $err = '';
  if ( isset($_POST["name"]) && filter_var($_POST['name'], FILTER_SANITIZE_STRING) ) {
    $name = test_input($_POST['name']);
  } else {
    $err.='Wrong name';
  }

  if ( isset($_POST["email"]) && filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) ) {
    $email = test_input($_POST['email']);
  } else {
    $err.='Incorrect email';
  }
  
  if ($err) {
    echo  "<p class=\"text-danger\">$err</p>";
    exit;
  }

  try 
  {
    $message = test_input($_POST['message']);
    /*
    // Настройки
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Включение логов работы
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@example.com';
    $mail->Password = 'your_email_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // TLS шифровка
    $mail->Port = 587;
    */
    //Set who the message is to be sent from
    $mail->setFrom('info@skydive.dp.ua', 'Skydive.dp.ua');
    //Set who the message is to be sent to
    $mail->addAddress('zeroturtle@ua.fm', '');		//кто получит
    $mail->addCC('zeroturtle@ua.fm', '');			//копия
    //Set an alternative reply-to address
    $mail->addReplyTo('no-replyto@skydive.dp.ua', 'Noreply');

    $mail->isHTML(true);
    $mail->CharSet = "utf-8";
    $mail->Subject = "заполнена контактная форма с ".$_SERVER['HTTP_REFERER'];
    $mail->Body = "От: $name <br> Email: $email <br> Сообщение: $message . <br>
                  Связяться с ним можно по email <a href=\"mailto: $email\" $email </a>";

    if(!$mail->send()) {
	echo "<p class=\"text-danger\">Ошибка при отправке сообщения. <br> Mailer Error:  {$mail->ErrorInfo}</p>";
    } else {
	echo ('<p class="text-success">Ваше сообщение получено, спасибо!</p>');
    }
  } 
  catch (Exception $error) {
    // Если возникло исключение при отправке запроса
    echo "<p class=\"text-warning\">Произошло исключение при отправке запроса:  {$mail->ErrorInfo}</p>";
  }
  finally {
    //    header('location: contact.html'); // редирект на index.php после выполнения скрипта
  }
}

//Validate Form Data
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>
