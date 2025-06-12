<?php

require_once "session.php";
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
/*
contains at least 8 characters
metcontains both lower (a-z) and upper case letters (A-Z)
metcontains at least one number (0-9) or a symbol
metdoes not contain your email address
metis not commonly used
*/
    $fullname = trim($_POST["name"]);
    $email = trim($_POST["email"]); //stripslashes
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirm_password"]);
    $newsLetter = isset($_POST["newsletter"]);

    $error = "";
    // Validate inputs
    if(empty($fullname)){
        $error .= "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($fullname))){
        $error .= "Username can only contain letters, numbers, and underscores.";
    } 
    // Validate password
    if(empty($password)){
        $error .= "Please enter a password.";     
    } elseif(strlen($password) < 8){
        $error .= "Password must have atleast 6 characters.";
    }
    // Check if passwords match
    // Validate confirm password
    if(empty($confirmPassword)){
       $error .= "Please confirm password.";     
    } elseif(empty($error) && ($password != $confirmPassword)){
       $error .= "Password did not match.";
    }

    if(empty($email)){
        $error .= "Email address is a mandatory field.";
    }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= "Invalid email address format!";
    } else {
        // Check if email are unique
      $row = $pdo->prepare("SELECT id FROM ACCOUNTS WHERE email = ?");
      $row->execute([$email]);
      $user = $row->fetch();
      if($user){
        $error .= "This email is already taken.";
      }
    }

    // Check input errors before inserting in database
    if(empty($error)){
      try {
        // Insert data into the database
        $pdo->prepare("INSERT INTO ACCOUNTS (username, password, email, newsletter) VALUES (?, ?, ?, ?)")->execute([$fullname, password_hash($password, PASSWORD_DEFAULT), $email, $newsLetter]);
/*
        // create temporary token
        $code = uniqid(true); // true for more uniqueness 
        $expFormat = mktime( date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y") ); // +1day
        $expDate = date("Y-m-d H:i:s",$expFormat);
        try {
          $pdo->prepare("INSERT INTO resetPasswords (code, email, expDate) VALUES(?,?,?)")->execute([$code, $email, $expDate]);
        }
        catch(PDOException $e) { echo $e->getMessage(); }
*/
        //отправить email-сообщение с линком для активации 
/*
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
        $mail->setFrom('no-replay@optimus.dp.ua', 'No Replay'); // from who? 
        $mail->addAddress($emailTo, '');     // Add a recipient

        //Content
        // this give you the exact link of you site in the right page 
        // if you are in actual web server, instead of http://" . $_SERVER['HTTP_HOST'] write your link 
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Your password reset link';
        $url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']). "/activate.php?code=$code"; 
        $smarty->assign("URL", $url);
        $mail->Body = $smarty->fetch('register.tpl');					//шаблон страницы соревнований
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
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
*/
        //показать на экране дальнейшие шаги.
      $error .=  '<div class="success"><p>Please check your email, follow a link in email to complete registration.</p></div><br />';

      }
      catch(PDOException $e) { echo $e->getMessage(); }
    }
}

require("register.html");
?>