<?php
// оформление подписки
require_once "auth.php";
require_once "config.php";
require_once "sendmail.php";

require 'libs/Smarty.class.php';

date_default_timezone_set('UTC');
$err='';

///////////////////////////////////
// проверяем корректность введенных данных
///////////////////////////////////
//Validate Form Data
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
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

//вспомогательная функция 
function filter(&$value) {
  $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
if (isset($_POST['types']) || count($_POST['types'])>0) {
  // для Free - все дисциплины, для Standard берем список из $_POST
  $TypeList['types'] = ($licenсeType==0) ? ['FS','SF','AE','CF','WS'] : $_POST['types'];
  array_walk_recursive($TypeList['types'], "filter");
} else {
  $err.="error disciplines";
}

// вернуться если были ошибки заполнения формы
if (!empty($err)) { 
  header('location: licence.html'); exit; 
}
///////////////////////////////////



///////////////////////////////////
// СОЗДАТЬ ЛИЦЕНЗИЮ
///////////////////////////////////
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

  Формат файла лицензии 
  TLicenceFile = packed record
    Licence: TLicence;
    CheckSum: string[32];	// md5-hash лицензии (32-character hexadecimal number)
    SecureKey: Cardinal;	// unsigned integer (4 байта)
  end;
*/

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
// array в число. Каждый бит соответствует типу дисциплины
function convert2bin($array) {				// array список дисциплин 
	$T = 0;
	for ($i = count($array)-1; $i>=0; $i--)
		$T = $T ^ (1 << $array[$i]);
	return $T;
}
// Создание лицензии по описанному формату
function makeLicence($form) {
	$License = [];
	// строку из UTF-8 надо ОБЕЗЯТЕЛЬНО перевести в однобайтовый код!
	$License['Owner']  = iconv("UTF-8", "Windows-1251", $form[0]);		// Владелец
	$License['Company'] = iconv("UTF-8", "Windows-1251", $form[1]);		// организация
	$License['Email']  = iconv("UTF-8", "Windows-1251", $form[2]);		// email 
	$License['Type'] = $form[3];						// тип подписки Standard/Personal, определяет количество консолей
	$License['EventType'] = convert2bin($form[4]);
	// автоматически заполняемые поля
	$License['Version'] = 4;						// версия формата данных
	$License['Number'] = GUID();//uniqid();						// сгенерить уникальный номер подписки string[32]
	$License['DateStart'] = new DateTime;					// дата выдачи 	
	$License['DateEnd'] = new DateTime('+365 day');				// срок действия до = +1год 
	date_time_set($License['DateStart'],0,0,0,0);				// приводим к формату strtotime("2025-01-01 00:00:00")
	date_time_set($License['DateEnd'],0,0,0,0);
	$License['Active'] = true;						// признак активной подписки, для новой всегда = true
	return $License;
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
function createLicenceFile($License) {
	$LicenseStr = 
		pack("C", $License['Version'])
		.pack("CA*", strlen($License['Number']), $License['Number']) 
		.pack("d", (date_timestamp_get($License['DateStart']) - strtotime("1899-12-30")) / 86400)
		.pack("d", (date_timestamp_get($License['DateEnd']) - strtotime("1899-12-30")) / 86400)
		.pack("CA*", strlen($License['Email']), $License['Email'].random_bytes(127-strlen($License['Email']))) 
		.pack("CA*", strlen($License['Company']), $License['Company'].random_bytes(127-strlen($License['Company']))) 
		.pack("CA*", strlen($License['Owner']), $License['Owner'].random_bytes(127-strlen($License['Owner']))) 
		.pack("C", (boolval($License['Type'])=='Standard' ? 5 : 1))		//QtyLicence - Максимальное количество портов, 5 для Standard или 1 для Personal
		.pack("v", $License['EventType'])					//unsigned short
		.pack("V", (boolval($License['Active'])==true ? 0xFFFFFFFF : 0))	//boolean занимает 4 байта!
		.pack("V", (boolval($License['Type'])=='Standard' ? 0xFFFFFFFF : 0));	//WebPublishing зависит от типа подписки
	$GLOBALS['CheckSum'] = md5($LicenseStr); 
	$LicenseStr.= pack("CA*", strlen($GLOBALS['CheckSum']), $GLOBALS['CheckSum']);		//добавить контрольную сумму лицензии

	// "шифруем" черз xor по байтам
	$ar = str_split( $LicenseStr );							// разбираем строку лицензии по байтам
	$SecureKey = random_int(1, PHP_INT_MAX);					// генерим случайный "секретный" ключ 4 байта
	$Key = pack('V', $SecureKey);							// разбираем ключ по байтам
	for ($i=0;$i<count($ar); $i++)  $ar[$i] = $ar[$i] ^ $Key[$i % 4];
	return  base64_encode(implode('',$ar).pack('V',$SecureKey));			//добавить к лицензии ключ  :) 
} //конец makeLicence


// Создание лицензии по данным из формы
$License = makeLicence([$owner, $company, $email, $licenсeType, type_list($TypeList['types'])]); 
// созхранить лицензию в файл 
$Licensefile = 'media/'.$License['Number'].'.lic';
file_put_contents( $Licensefile, implode(PHP_EOL, str_split(createLicenceFile($License),64)) );	 //делим шоб выглядело красиво :)

// Сохраняем данные подписки в БД 
// без активации
$version = parse_ini_file('version.info', true);
$query = "INSERT INTO LICENCE(NUMBER, NAME, EMAIL, TITLE, COMPANY, LICENCETYPE, EVENTTYPES, DateStart, DateEnd, LICENCEHASH, ACCOUNT_ID, VERSION) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
$stmt= $pdo->prepare($query);
$stmt->execute([$License['Number'], $License['Owner'], $License['Email'], $title, $License['Company'], $License['Type'], $License['EventType'],
         date_format($License['DateStart'], 'Y-m-d H:i:s'), date_format($License['DateEnd'], 'Y-m-d H:i:s'), $CheckSum, $_SESSION['account_id'],
         implode(';', array_map( function ($v, $k) { return $k.'='.$v; }, $version['Apps'], array_keys($version['Apps']) ))]);
// получить ID ?
///////////////////////////////////


///////////////////////////////////
//активация лицензии
function activateLicanse($Number) {
  global $pdo;
  $pdo->prepare("UPDATE LICENCE SET ACTIVE=true WHERE Number=?")->execute([$Number]);
}
// отправить письмо с файлом лицензии
function sendLicanse($License) {
  // считать шаблон письма
  $smarty = new Smarty;
  $smarty->debugging = false;
  $smarty->caching = false;
  $smarty->cache_lifetime = 300;
  $smarty->assign("licence", $License);
  $smarty->assign("type", ($License['Type']==1?'Site':'Single'));
  $smarty->assign("datestart", date_format($License['DateStart'],'Y-m-d'));
  $smarty->assign("dateend", date_format($License['DateEnd'],'Y-m-d '));
  $smarty->assign("desc", $TypeList['types']);
  $text = $smarty->fetch('new_subscription.tpl'); 
  send($text, $Licensefile, $License);
}

///////////////////////////////////
// Делать в зависимости от типа лицензии
switch ($License['Type']) 
{
  case 0: activateLicanse($License["Number"]); //single
          sendLicanse($License);
          header('location: thanks.html'); // редирект на index.php после выполнения скрипта
          break;
  case 1:  //site
          //отправить в банк на оплату
          break;
  default ;
}
// банк вызывает callback-скрипт, где вызвать
// activateLicanse($License["Number"]); sendLicanse($License);



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