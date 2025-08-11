<?php
// оформление подписки
// ВАЖНО! Хранить в секрете код, т.к. он расскрывает формат лицензии

if ( !session_id() ) @session_start();

require_once __DIR__ . '/auth/config/database.php';
require_once __DIR__ . '/auth/src/libs/connection.php';

date_default_timezone_set('UTC');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

require 'libs/Smarty.class.php';

//header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');


///////////////////////////////////
// ОПИСАНИЕ ФОРМАТА ПОЛЕЙ ЛИЦЕНЗИИ
///////////////////////////////////
/*
  TLicenceW = packed record
    Version: Byte;                  // версия формата данных, reserved
    Number: String[36];             // внутренний номер подписки, GUID всегда содержит только A..Z0..9-
    Email,                          // email для связи
    Company,                        // название организации
    Owner: TByteA;                  // Владелец  FirstName + LastName  array[0..127*SizeOf(Char)] of Byte;
    DateStart,                      // дата выдачи и конец лицензии
    DateEnd: TDateTime;             
    EventType: TEventSet;           // список разрешенных дисциплин, каждый бит соответствует типу
    QtyLicence: Byte;               // Максимальное количество портов, 5 для Standard или 1 для Personal
    WebPublishing: BOOL;            // разрешена публикация на Web-сайт результатов (только для Standard подписки)
    Active: BOOL;                   // признак активной лицензии
  end;


  Формат файла лицензии 
  TLicenceFile = packed record
    Licence: TLicence;
    CheckSum: string[32];	// md5-hash лицензии (32-character hexadecimal number)
    SecureKey: Cardinal;	// unsigned integer (4 байта)
  end;
*/

// array в число. Каждый бит соответствует типу дисциплины
function convert2bin($array) {				// array список дисциплин 
	$T = 0;
	for ($i = count($array)-1; $i>=0; $i--)
		$T = $T ^ (1 << $array[$i]);
	return $T;
}
//generate valid version 4 UUIDs xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

// Создание лицензии по описанному формату
function makeLicence($form) 
{
	$License = [];
	$License['Owner']  = mb_substr(mb_convert_encoding($form[0], 'UTF-8', 'auto'), 0, 127*2);       // Владелец   
	$License['Company'] = mb_substr(mb_convert_encoding($form[1], 'UTF-8', 'auto'), 0, 127*2);      // организация
	$License['Email']  = mb_substr(mb_convert_encoding($form[2], 'UTF-8', 'auto'), 0, 127*2);       // email      
	$License['Type'] = $form[3];						// тип подписки Standard/Personal, определяет количество консолей
	$License['EventType'] = convert2bin($form[4]);
	// автоматически заполняемые поля
	$License['Version'] = 5;						// версия формата данных
	$License['Number'] = GUID();//uniqid();					// сгенерить уникальный номер подписки string[32]
	$License['DateStart'] = new DateTime;					// дата выдачи 	
	$License['DateEnd'] = new DateTime('+1 year');				// срок действия до = +365 day 
	date_time_set($License['DateStart'],0,0,0,0);				// приводим к формату strtotime("2025-01-01 00:00:00")
	date_time_set($License['DateEnd'],0,0,0,0);
	$License['Active'] = true;						// признак активной подписки, для новой всегда = true
	return $License;
}

function createLicenceFile($License) 
{
	$LicenseStr = 
		pack("C", $License['Version'])
		.pack("CA*", strlen($License['Number']), $License['Number']) 
		.pack("VA*", strlen($License['Email']), $License['Email'].random_bytes(127*2-3-strlen($License['Email']))) 
		.pack("VA*", strlen($License['Company']), $License['Company'].random_bytes(127*2-3-strlen($License['Company']))) 
		.pack("VA*", strlen($License['Owner']), $License['Owner'].random_bytes(127*2-3-strlen($License['Owner']))) 
		.pack("d", (date_timestamp_get($License['DateStart']) - strtotime("1899-12-30")) / 86400)   // delphi ведет отсчет DateTime от этой даты "1899-12-30" :)
		.pack("d", (date_timestamp_get($License['DateEnd']) - strtotime("1899-12-30")) / 86400)
		.pack("v", $License['EventType'])					// unsigned short
		.pack("C", (boolval($License['Type'])=='Standard' ? 5 : 1))		// QtyLicence - Максимальное количество портов, 5 для Standard или 1 для Personal
		.pack("V", (boolval($License['Type'])=='Standard' ? 0xFFFFFFFF : 0))	// WebPublishing зависит от типа подписки
		.pack("V", (boolval($License['Active'])==true ? 0xFFFFFFFF : 0));	// boolean занимает 4 байта!
/*
		$l = 0;
		$l += strlen(pack("C", $License['Version']));
		echo $l.'Number='; $l += strlen(pack("CA*", strlen($License['Number']), $License['Number']) ) ;
		echo $l.'Email='; $l += strlen(pack("VA*", mstrlen($License['Email']), $License['Email'].random_bytes(127*2-3-strlen($License['Email']))) );
		echo $l.'Company='; $l += strlen(pack("VA*", strlen($License['Company']), $License['Company'].random_bytes(127*2-3-strlen($License['Company']))) );
		echo $l.'Owner='; $l += strlen(pack("VA*", strlen($License['Owner']), $License['Owner'].random_bytes(127*2-3-strlen($License['Owner']))) );
		echo $l.'DateStart='; $l += strlen(pack("d", (date_timestamp_get($License['DateStart']) - strtotime("1899-12-30")) / 86400)) ;
		echo $l.'DateEnd='; $l += strlen(pack("d", (date_timestamp_get($License['DateEnd']) - strtotime("1899-12-30")) / 86400) );
		echo $l.'EventType='; $l += strlen(pack("v", $License['EventType']));
		echo $l.'QtyLicence='; $l += strlen(pack("C", (boolval($License['Type'])=='Standard' ? 5 : 1))	);
		echo $l.'WebPublishing='; $l += strlen(pack("V", (boolval($License['Type'])=='Standard' ? 0xFFFFFFFF : 0)));
		echo $l.'Active='; $l += strlen(pack("V", (boolval($License['Active'])==true ? 0xFFFFFFFF : 0)));
die;    */
	$GLOBALS['CheckSum'] = md5($LicenseStr); 
	$LicenseStr.= pack("CA*", strlen($GLOBALS['CheckSum']), $GLOBALS['CheckSum']);		//добавить контрольную сумму лицензии

	// "шифруем" черз xor по байтам
	$ar = str_split( $LicenseStr );							// разбираем строку лицензии по байтам
	$SecureKey = random_int(1, PHP_INT_MAX);					// генерим случайный "секретный" ключ 4 байта
	$Key = pack('V', $SecureKey);							// разбираем ключ по байтам
	for ($i=0;$i<count($ar); $i++)  $ar[$i] = $ar[$i] ^ $Key[$i % 4];
	return  base64_encode(implode('',$ar).pack('V',$SecureKey));			//добавить к лицензии ключ  :) 
} 
///////////////////////////////////

//активация лицензии
function activateLicanse($Number) 
{
  db()->prepare("UPDATE LICENCE SET ACTIVE=true WHERE Number=?")->execute([$Number]);
}

function read_template(array $License, array $TypeList): string
{
  // считать шаблон письма
  $smarty = new Smarty;
  $smarty->debugging = false;
  $smarty->caching = false;
  $smarty->cache_lifetime = 300;
  $smarty->assign("owner", $License['Owner']);
  $smarty->assign("type", ($License['Type']==1?'Site':'Single'));
  $smarty->assign("datestart", date_format($License['DateStart'],'Y-m-d'));
  $smarty->assign("dateend", date_format($License['DateEnd'],'Y-m-d '));
  $smarty->assign("desc", $TypeList);

  return $smarty->fetch('new_subscription.tpl'); 
}

// отправить письмо с файлом лицензии
function sendLicense($file, $License, $TypeList) 
{
  // отправка письма
  $mail = new PHPMailer(true);
  $mail->isSMTP();                                            // Send using SMTP
  $mail->Host       = 'smtp.example.com';                     // Set the SMTP server to send through
  $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
  $mail->Username   = 'your_email@example.com';               // SMTP username
  $mail->Password   = 'your_password';                        // SMTP password
  $mail->SMTPSecure = 'tls'; // Enable TLS encryption
  $mail->Port       = 587; 

  $mail->setFrom('info@skydive.dp.ua', 'Optimus info');
  $mail->addAddress($Licence['Email'], $Licence['Owner']);
  $mail->addBCC('zeroturtle@ua.fm', '');
  $mail->addReplyTo('no-replyto@skydive.dp.ua', 'Noreply');
  $mail->isHTML(true);
  $mail->Subject = 'Thank You for subscribe OPTIMUS';
  $mail->msgHTML( read_template($License, 
                array_intersect_key(["FS"=>'Formation Skydiving',"SF"=>'Speed Formation Skydiving',"AE"=>'Artistic Events',"CF"=>'Canopy Formation',"WS"=>'Wingsuit Flying'], array_fill_keys($TypeList,''))) ); 
  $mail->AddAttachment($file);
  try {
    $mail->send();
    echo 'Message has been sent';
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }  
}

// привести к именованному массиву ['types']= [Type_ID], чтоб конвертить в json
function type_list( $array ) {
  // список всех дисциплин
$disciplines = ['FS'=>[1],'SF'=>[2,3],'AE'=>[4],'CF'=>[9],'IS'=>[5],'DS'=>[6],'DY'=>[10,11],'WS'=>[7,12],'CP'=>[8],'SP'=>[13]];  
  $F_Types=[];
  foreach( array_unique($array) as $t )
    if (array_key_exists($t, $disciplines)) { // убедимся, что в массиве корректные данные
      $F_Types = array_merge($F_Types, array_values($disciplines[$t]));
    }
  return $F_Types;
}

//Validate Form Data
function test_input($data) 
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data; //mb_convert_encoding($data, 'UTF-8', 'auto');
}

//вспомогательная функция 
function filter(&$value) 
{
  $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}


///////////////////////////////////
// проверяем входные данные
///////////////////////////////////
$err='';

//тип лицензии 1-standard 0-personal
if ( isset($_POST["licence"]) && (filter_var($_POST['licence'],FILTER_VALIDATE_INT)!==false)) {
  $licenсeType = intval($_POST['licence']);
} else {
  $err.=" error licence type";
}

if ( (isset($_POST["first_name"]) && (filter_var($_POST['first_name'], FILTER_SANITIZE_STRING)!==false)) &&
     (isset($_POST["last_name"]) && (filter_var($_POST['last_name'], FILTER_SANITIZE_STRING)!==false)) ) {
  $owner =  test_input($_POST['first_name']).' '.test_input($_POST['last_name']);
} else {
  $err.=" error owner";
}

if ( isset($_POST["email"]) && (filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)!==false)) {
  $email =  test_input($_POST['email']);
} else {
  $err.=" error email";
}

if (isset($_POST["company"]) && (filter_var($_POST['company'], FILTER_SANITIZE_STRING)!==false)) {
  $company =  test_input($_POST['company']);
} else {
  $err.=" error company name";
}

if ( isset($_POST["jobtitle"]) && (filter_var($_POST['jobtitle'], FILTER_SANITIZE_STRING)!==false)) {
  $title = test_input($_POST['jobtitle']);
} else {
  $err.=" error job title";
}

$T = explode(',',(isset($_POST['types']) ? $_POST['types'] : ''));
if (count($T)>0) {
  // для Free - все дисциплины, для Standard берем список из $_POST
  $TypeList = ($licenсeType==0) ? ['FS','SF','AE','CF','WS'] : $T;
  array_walk_recursive($TypeList, "filter");
} else {
  $err.="error disciplines";
}

// вернуться если были ошибки заполнения формы
if (!empty($err)) { 
  header('location: licence.html'); exit; 
}


// Создание лицензии по данным из формы
$License = makeLicence([$owner, $company, $email, $licenсeType, type_list($TypeList)]); 

// созхранить лицензию в файл 
$Licensefile = 'media/'.$License['Number'].'.lic';
file_put_contents( $Licensefile, implode(PHP_EOL, str_split(createLicenceFile($License),64)) );	 //делим частями шоб выглядело красиво :)

// Сохраняем подписку в БД 
//$version = parse_ini_file('version.info', true);
$query = "INSERT INTO LICENCE(NUMBER, NAME, EMAIL, TITLE, COMPANY, LICENCETYPE, EVENTTYPES, DateStart, DateEnd, LICENCEHASH, ACCOUNT_ID, VERSION) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
$stmt = db()->prepare($query);
$stmt->execute([$License['Number'], $License['Owner'], $License['Email'], $title, $License['Company'], $License['Type'], $License['EventType'],
         date_format($License['DateStart'], 'Y-m-d H:i:s'), date_format($License['DateEnd'], 'Y-m-d H:i:s'), $GLOBALS['CheckSum'], $_SESSION['user_id'],
         '' //implode(';', array_map( function ($v, $k) { return $k.'='.$v; }, $version['Apps'], array_keys($version['Apps']) ))]
         ]);

// Делать в зависимости от типа лицензии
switch ($License['Type']) 
{
  case 0: //single
          activateLicanse($License["Number"]);  //активируем сразу
          //sendLicense($Licensefile, $License);
          header('location: thanks.html'); // редирект на index.php после выполнения скрипта
          break;
  case 1:  //site
          //отправить на оплату в банк
          //для банка создать callback-скрипт, где вызвать
          //activateLicanse($License["Number"]); sendLicense($Licensefile, $License);
          header('location: thanks.html'); // редирект на index.php после выполнения скрипта
          break;
  default ;
}


///////////////////////////////////
/*
// пример как прочитать лицензию из файла на php
  $fileContent = base64_decode($fileContent);
// Convert the file content to a byte array
  $byteArray = unpack('C*', $fileContent);
// Получить секретный ключ (крайние 4 байта)
  $SKey = array_slice( $byteArray, -4, 4 );
  $SecureKey =   ($SKey[3] << 24) + ($SKey[2] << 16) + ($SKey[1] << 8) + $SKey[0];
  echo $SecureKey.PHP_EOL;   
*/

?>