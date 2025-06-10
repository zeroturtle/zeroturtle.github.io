<?php
// оформление подписки
require_once "auth.php";
require_once "config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

require 'libs/Smarty.class.php';

date_default_timezone_set('UTC');
$err='';

// проверяем корректность введенных данных
//тип лицензии 1-standard 0-personal
if ( isset($_POST["licence"]) && (filter_var($_POST['licence'],FILTER_VALIDATE_INT)!==false)) {
  $licenсeType = intval(test_input($_POST['licence']));
} else {
  $err.=" error licence type";
}

if ( (isset($_POST["first_name"]) && (filter_var($_POST['first_name'], FILTER_SANITIZE_STRING)!==false)) ||
     (isset($_POST["last_name"]) && (filter_var($_POST['last_name'], FILTER_SANITIZE_STRING)!==false)) ) {
  $owner =  test_input($_POST['first_name']).' '.test_input($_POST['last_name']);
} else {
  $err.=" error owner";
}

if ( isset($_POST["email"]) && (filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)!==false)) {
  $email =  test_input($_POST['email']);
}

if (isset($_POST["company"]) && (filter_var($_POST['company'], FILTER_SANITIZE_STRING)!==false)) {
  $company =  test_input($_POST['company']);
} else {
  $err.=" error company";
}

if ( isset($_POST["jobtitle"]) && (filter_var($_POST['jobtitle'], FILTER_SANITIZE_STRING)!==false)) {
  $title = test_input($_POST['jobtitle']);
}

if (count($_POST['types'])==0) {
  $err.="error disciplines";
}

// вернуться если были ошибки заполнения формы
if (!empty($err)) { 
  header('location: licence.html'); exit; 
}


// для Free - ысе дисциплины, для Standard берем список из $_POST
$TypeList['types'] = ($licenсeType==0) ? ['FS','SF','AE','CF','WS'] : $_POST['types'];
array_walk_recursive($TypeList['types'], "filter");

// Создание лицензии по данным из формы
$Licence = makeLicence([$owner, $company, $email, $licenсeType, type_list($TypeList['types'])]); 

// созхранить лицензию в файл 
$Licencefile = 'media/'.$Licence['Number'].'.lic';
file_put_contents( $Licencefile, implode(PHP_EOL, str_split(createLicenceFile($Licence),64)) );	 //делим шоб выглядело красиво :)

// Сохраняем подписку в БД
$version = parse_ini_file('version.info', true);
$query = "INSERT INTO LICENCE(NUMBER, NAME, EMAIL, TITLE, COMPANY, LICENCETYPE, EVENTTYPES, DateStart, DateEnd, LICENCEHASH, ACCOUNT_ID, VERSION) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
$stmt= $pdo->prepare($query);                                                   
$stmt->execute([$Licence['Number'], $Licence['Owner'], $Licence['Email'], $title, $Licence['Company'], $Licence['Type'], $Licence['EventType'],
         date_format($Licence['DateStart'], 'Y-m-d H:i:s'), date_format($Licence['DateEnd'], 'Y-m-d H:i:s'), $CheckSum, $_SESSION['account_id'],
         implode(';', array_map( function ($v, $k) { return $k.'='.$v; }, $version['Apps'], array_keys($version['Apps']) ))]);


// отправить письмо с файлом лицензии
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->caching = false;
$smarty->cache_lifetime = 300;
$smarty->assign("licence", $Licence);
$smarty->assign("type", ($Licence['Type']==1?'Standard':'Personal'));
$smarty->assign("datestart", date_format($Licence['DateStart'],'Y-m-d'));
$smarty->assign("dateend", date_format($Licence['DateEnd'],'Y-m-d '));
$smarty->assign("desc", $TypeList['types']);
$template = $smarty->fetch('new_subscription.tpl'); 

// отправка письма
$mail = new PHPMailer(true);
$mail->CharSet = "utf-8";
$mail->isHTML(true);
$mail->setFrom('webmaster@skydive.dp.ua', 'Skydive.dp.ua');
$mail->addAddress($Licence['Email'], $Licence['Owner']);
$mail->addBCC('zeroturtle@ua.fm', '');
$mail->addReplyTo('no-replyto@skydive.dp.ua', 'Noreply');
$mail->Subject = 'Thank You for subscribe OPTIMUS';
$mail->AddAttachment($Licencefile);
$mail->msgHTML($template);
if (!$mail->send()) {
	$err.="Mailer Error: ".$mail->ErrorInfo;
} else {
	$err.="Message sent!";
}
$mail->ClearAllRecipients(); // reset the `To:` list to empty
// конец отправки


header('location: thanks.html'); // редирект на index.php после выполнения скрипта


///////////////////////////////////////////////////////
//============================================
/*  Описание формата полей лицензии
  TLicence = packed record
    Version : byte;                  // версия формата данных, reserved
    Number : String[32];             // внутренний номер подписки, используется в качестве ключа для идентификации (!) 5символов маловато..  надо бы сделать длиннее
    DateStart, DateEnd: TDateTime;   // дата выдачи и конец лицензии
    Email     : String[127];         // email для связи
    Company   : String[127];         // название организации
    Owner : String[127];             // Владелец  FirstName + LastName
    QtyLicence: byte;                // Максимальное количество портов, 5 для Standard или 1 для Personal
    EventType : TEventSet;           // список разрешенных дисциплин, каждый бит соответствует типу
    Active : BOOL;                   // признак активной лицензии
    WebPublishing : BOOL;            // разрешена публикация на Web-сайт результатов (только для Standard подписки)
  end;

  т.к. Null-terminated string легко определяются, для представления строк используется стиль хранения pascal
  где 0-байт хранит длину строки, а не значащие символы заполняются "мусором"
  чтоб заполнить нулями вместо random_bytes использовать str_pad($Number,32,chr(0))
  delphi ведет отсчет DateTime от этой даты "1899-12-30" :)

  Поля файла лицензии 
  TLicenceFile = packed record
    Licence: TLicence;
    CheckSum: string[32];	// md5-hash лицензии (32-character hexadecimal number)
    SecureKey: Cardinal;	// unsigned integer (4 байта)
  end;
*/

//Validate Form Data
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
// array в число. Каждый бит соответствует типу дисциплины
function convert2bin($array) {				// array список дисциплин 
	$T = 0;
	for ($i = count($array)-1; $i>=0; $i--)
		$T = $T ^ (1 << $array[$i]);
	return $T;
}
// Создание лицензии по описанному формату
function makeLicence($form) {
	$Licence = [];
	// строку из UTF-8 надо ОБЕЗЯТЕЛЬНО перевести в однобайтовый код!
	$Licence['Owner']  = iconv("UTF-8", "Windows-1251", $form[0]);		// Владелец
	$Licence['Company'] = iconv("UTF-8", "Windows-1251", $form[1]);		// организация
	$Licence['Email']  = iconv("UTF-8", "Windows-1251", $form[2]);		// email 
	$Licence['Type'] = $form[3];						// тип подписки Standard/Personal, определяет количество консолей
	$Licence['EventType'] = convert2bin($form[4]);
	// автоматически заполняемые поля
	$Licence['Version'] = 4;						// версия формата данных
	$Licence['Number'] = GUID();//uniqid();						// сгенерить уникальный номер подписки string[32]
	$Licence['DateStart'] = new DateTime;					// дата выдачи 	
	$Licence['DateEnd'] = new DateTime('+365 day');				// срок действия до = +1год 
	date_time_set($Licence['DateStart'],0,0,0,0);				// приводим к формату strtotime("2025-01-01 00:00:00")
	date_time_set($Licence['DateEnd'],0,0,0,0);
	$Licence['Active'] = true;						// признак активной подписки, для новой всегда = true
	return $Licence;
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
// ВАЖНО! Хранить в секрете код, т.к. он расскрывает формат лицензии
// строка лицензии 
function createLicenceFile($Licence) {
	$LicenceStr = 
		pack("C", $Licence['Version'])
		.pack("CA*", strlen($Licence['Number']), $Licence['Number']) 
		.pack("d", (date_timestamp_get($Licence['DateStart']) - strtotime("1899-12-30")) / 86400)
		.pack("d", (date_timestamp_get($Licence['DateEnd']) - strtotime("1899-12-30")) / 86400)
		.pack("CA*", strlen($Licence['Email']), $Licence['Email'].random_bytes(127-strlen($Licence['Email']))) 
		.pack("CA*", strlen($Licence['Company']), $Licence['Company'].random_bytes(127-strlen($Licence['Company']))) 
		.pack("CA*", strlen($Licence['Owner']), $Licence['Owner'].random_bytes(127-strlen($Licence['Owner']))) 
		.pack("C", (boolval($Licence['Type'])=='Standard' ? 5 : 1))		//QtyLicence - Максимальное количество портов, 5 для Standard или 1 для Personal
		.pack("v", $Licence['EventType'])					//unsigned short
		.pack("V", (boolval($Licence['Active'])==true ? 0xFFFFFFFF : 0))	//boolean занимает 4 байта!
		.pack("V", (boolval($Licence['Type'])=='Standard' ? 0xFFFFFFFF : 0));	//WebPublishing зависит от типа подписки
	$GLOBALS['CheckSum'] = md5($LicenceStr); 
	$LicenceStr.= pack("CA*", strlen($GLOBALS['CheckSum']), $GLOBALS['CheckSum']);		//добавить контрольную сумму лицензии

	// "шифруем" черз xor по байтам
	$ar = str_split( $LicenceStr );							// разбираем строку лицензии по байтам
	$SecureKey = random_int(1, PHP_INT_MAX);					// генерим случайный "секретный" ключ 4 байта
	$Key = pack('V', $SecureKey);							// разбираем ключ по байтам
	for ($i=0;$i<count($ar); $i++)  $ar[$i] = $ar[$i] ^ $Key[$i % 4];
	return  base64_encode(implode('',$ar).pack('V',$SecureKey));			//добавить к лицензии ключ  :) 
} //конец makeLicence
//============================================

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

function get_template($view, $item) {
global $images;
	$path='./tmpl/';	//путь где хранятся шаблоны
	switch ($view) {	//шаблонов писем:
	  case 'reminder30':		$template="renew_reminder30.html"; break;	//напоминание про окончание подписки за 30 дней
	  case 'reminder14':		$template="renew_reminder14.html"; break;	//напоминание про окончание подписки за 14 дней
	  case 'licence_expired': 	$template="renew_licence_expired.html"; break;	//предупреждение об окончании срока подписки
	  case 'new_subscription': 	$template="new_subscription.html"; break;	//отправка новая лицензия
	  default    : 			$template="news_review.html";			//рассылка новости
	}
	$header   = file_get_contents($path."header.html");
	$footer   = file_get_contents($path."footer.html");
	$template = file_get_contents($path.$template);

	$data = array('number'=>$item['Number'], 
		'name'=>$item['Owner'],
		'datestart'=>$item['DateStart'],
		'dateend'=>$item['DateEnd'],
		'link'=>$item['link'],
		'type'=>$item['Type'],
		'desc'=>$item['EventType']	//здесь можно сделать список названий типов
	);
	if (preg_match_all("/{{(.*?)}}/", $template, $m))
		foreach ($m[1] as $i => $varname) 
			$template = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $template);
	$page = $header.$template.$footer;
return $page;
}

function filter(&$value) {
  $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
   
/*
// пример как прочитать лицензию из файла
   $fileContent = base64_decode($fileContent);

// Convert the file content to a byte array
$byteArray = unpack('C*', $fileContent);

// Получить секретный ключ (крайние 4 байта)
$SKey = array_slice( $byteArray, -4, 4 );
$SecureKey =   ($SKey[3] << 24) + ($SKey[2] << 16) + ($SKey[1] << 8) + $SKey[0];
echo $SecureKey.PHP_EOL;   */

?>