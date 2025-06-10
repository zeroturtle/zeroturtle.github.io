<?php
require_once "session.php";
require_once "config.php";

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/Smarty.class.php';
$smarty = new Smarty;
//$smarty->force_compile = true;
$smarty->debugging = false;
$smarty->caching = false;
$smarty->cache_lifetime = 300;


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
  // Validate email
  if(!empty(trim($_POST["email"])) || filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
    // create temporary token
    $emailTo = trim($_POST['email']);
    $code = uniqid(true); // true for more uniqueness 
    $expFormat = mktime( date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y") );
    $expDate = date("Y-m-d H:i:s",$expFormat);
    try {
      $pdo->prepare("INSERT INTO resetPasswords (code, email, expDate) VALUES(?,?,?)")->execute([$code, $emailTo, $expDate]);
    }
    catch(PDOException $e) { echo $e->getMessage(); }

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //Server settings
        //$mail->SMTPDebug = 0;     // Enable verbose debug output, 1 for produciton , 2,3 for debuging in devlopment 
        $mail->isSMTP();                                      // Set mailer to use SMTP
        // $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        // $mail->Port = 587;   // for tls                                 // TCP port to connect to
        $mail->Port = 465;
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->Username = 'email@gmail.com';                 // SMTP username
        $mail->Password = 'password';                           // SMTP password

        //Recipients
        $mail->setFrom('my-email@optimus-domain.com', 'OPTIMUS'); // from who? 
        $mail->addAddress($emailTo, '');     // Add a recipient
        $mail->addReplyTo('no-replay@example.com', 'No Replay');

        //Content
        // this give you the exact link of you site in the right page 
        // if you are in actual web server, instead of http://" . $_SERVER['HTTP_HOST'] write your link 
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Your password reset link';
        $url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']). "/resetpwd.html?code=$code"; 
        $smarty->assign("URL", $url);
        $mail->Body = $smarty->fetch('recover.tpl');					//шаблон страницы соревнований
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        // to solve a problem 
        $mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        );
        $mail->send();
        // redirect to login page
        header("location: login.php");
        exit;
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
  } else{
    echo "Please confirm the email.";
  }
}

require("recover.html");
?>