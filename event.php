<?php
//Создание нового соренования (вставки COMPETITION_ID)
// Получаем json-файл вида: '{"id": int,"name": "string", "location": "string", "date_start": "string Y-m-d", "date_end": "string Y-m-d", "type_id": int, "event": [{"id": int, "name": "string"},...{}]}';
//для авторизации используется NUMBER и HASH лицензии 

// Include config file
require_once "config.php";

define('EVENTS_DIR', 'events/');
define('EVENT_TEMPLATE_DIR', EVENTS_DIR.'template/');

// если json отправлен в теле зароса
//$json = file_get_contents('php://input'); 
	//$_POST['JSON']=iconv("Windows-1251", "UTF-8", $json); // привезти к UTF
	//$data = json_decode($json, true);    print_r($data);  die();

// отклоняем запрос при неправильном json PHP>=8.3.0
//if( !json_validate($_POST['JSON']) ) { die('invalid json') }
$event = json_decode($_POST['JSON']); 
if ( json_last_error() != JSON_ERROR_NONE) { die(json_last_error_msg()); }
// реквизиты соревнования
$COMPETITION_NAME = $event->name;
$TYPE = $event->type_id;      //это создаваемый тип соревнований
$DATE_FROM = $event->date_start;
$DATE_TO = $event->date_end;
$PLACE = $event->location;
$RANK = $event->events;
$THEME = $event->theme;


// check POST variables
$NUMBER = ( isset($_POST["NUMBER"]) || !ctype_xdigit($_POST["NUMBER"])) ? $_POST["NUMBER"] : 0;
if (!isset($_POST["NUMBER"]) || !ctype_xdigit($_POST["HASH"])) { die ('Incorrect hash'); } 
$HASH = $_POST["HASH"];


//////////////////////////////
//Licence_Validation
$query = "SELECT * FROM LICENCE WHERE ACTIVE=true" 
." AND NOW() BETWEEN DATESTART AND DATEEND"	//дата в диапазоне срока действия лицензии
." AND LICENCETYPE=1"			//Site
//." AND NUMBER = ?"			//номер лицензии
//." AND LICENCEHASH = ?"		//сверяем md5hash
." AND (EVENTTYPES & (1 << ?)) <> 0";	//тип соревнования входит в список дисциплин лицензии
$stmt = $pdo->prepare($query);
//$stmt->execute([$NUMBER, $HASH, $TYPE]); 
$stmt->execute([$TYPE]); 
$row = $stmt->fetch();
if ( is_null($row['LICENCE_ID']) ) die('Error licence validation or Incompatible type!');
$LicID = $row['LICENCE_ID'];

//блокируем повторный запрос создания такого же event
//$query="SELECT * FROM COMPETITION WHERE LICENCE_ID = ? AND DESCRIPTION->'$.id' = ?";            //MySQL
$query="SELECT * FROM COMPETITION WHERE LICENCE_ID = ? AND JSON_VALUE(DESCRIPTION, '$.id') = ?";  //MariaDB
$stmt = $pdo->prepare($query);
$stmt->execute([$LicID, $event->id]); 
$row = $stmt->fetch();
if (!isset($row)) { die('Event already exists!'); }
/////////////////////////////


//сохраняем event в БД
$stmt= $pdo->prepare("INSERT INTO COMPETITION (DESCRIPTION,LICENCE_ID) VALUES(?,?)")->execute([json_encode($event), 40]);
$row = $pdo->query("SELECT LAST_INSERT_ID();")->fetch();
$COMPETITION_ID = $row[0];     // COMPETITION_ID
$D = EVENTS_DIR.$COMPETITION_ID;
rrmdir($D);            // на всякий случай удаляем все 
mkdir($D, 0777, true); //надо так, но не рабоатет if (!mkdir($D, 0777, true)) { die('Failed to create directories...'); };
// скопировать из шаблона все файлы в папку event'а
rcopy(EVENT_TEMPLATE_DIR, $D);


// настроить переменные шаблона index.html
require 'libs/Smarty.class.php';
$smarty = new Smarty;
//$smarty->force_compile = true;
$smarty->debugging = false;
$smarty->caching = false;
$smarty->cache_lifetime = 300;

$smarty->assign("COMPETITION_ID", $COMPETITION_ID);
$smarty->assign("COMPETITION_NAME", $COMPETITION_NAME);
$smarty->assign("DATE_FROM", $DATE_FROM);
$smarty->assign("DATE_TO", $DATE_TO);
$smarty->assign("PLACE", $PLACE);
$smarty->assign("RANK", $RANK);
$smarty->assign("BASEURL", "http://{$_SERVER['SERVER_NAME']}/{$EVENTS_DIR}{$COMPETITION_ID}/");
$smarty->assign("THEME", (isset($THEME) && in_array($THEME,['dark','light'])) ? $THEME : "light");
$smarty->assign("PAGE_LOGO", 'Title.jpg');
$output = $smarty->fetch('index.tpl');					//шаблон страницы соревнований
file_put_contents(EVENTS_DIR.$COMPETITION_ID.'/index.html',$output);	// write to file event в папку COMPETITION_ID


//$PAGE_LOGO = isset($_FILES['page_logo']) ? $_FILES['page_logo'] : 'Title.jpg';
//organizer_logo
//competition_logo

// вернуть URL папки event'а
echo "http://{$_SERVER['SERVER_NAME']}/{EVENTS_DIR}{$COMPETITION_ID}/";



/////////////////////////////////////////////////////
// from https://www.php.net/manual/ru/function.copy.php example
// removes files and non-empty directories
function rrmdir($dir) {
  if (is_dir($dir)) {
    $files = scandir($dir);
    foreach ($files as $file)
    if ($file != "." && $file != "..") rrmdir("$dir/$file");
    rmdir($dir);
  }
  else if (file_exists($dir)) unlink($dir);
} 

// copies files and non-empty directories
function rcopy($src, $dst) {
  if (file_exists($dst)) rrmdir($dst);
  if (is_dir($src)) {
    mkdir($dst);
    $files = scandir($src);
    foreach ($files as $file)
    if ($file != "." && $file != "..") rcopy("$src/$file", "$dst/$file"); 
  }
  else if (file_exists($src)) copy($src, $dst);
}
?>