<?php
//Создание нового соренования (вставки COMPETITION_ID)
// Получаем json-файл вида: '{"id": int,"name": "string", "location": "string", "date_start": "string Y-m-d", "date_end": "string Y-m-d", "type_id": int, "theme": "string", "title_img": "string base64", "event": [{"id": int, "name": "string"},...{}]}';
//для авторизации используется NUMBER и HASH лицензии 

// Include config file
require_once "config.php";

define('EVENTS_DIR', 'events/');
define('EVENT_TEMPLATE_DIR', EVENTS_DIR.'template/');


function Licence_Validation($number,$hash,$type) {
  $query = "SELECT * FROM LICENCE WHERE ACTIVE=true" 
    ." AND NOW() BETWEEN DATESTART AND DATEEND"	//дата event'а в диапазоне срока действия лицензии
//    ." AND LICENCETYPE=1"			//только для Site-лицензии
    ." AND NUMBER = ?"				//номер лицензии
    ." AND LICENCEHASH = ?"			//сверяем md5hash
    ." AND (EVENTTYPES & (1 << ?)) <> 0";	//тип соревнования входит в список дисциплин лицензии
  $stmt = db()->prepare($query);
  $stmt->execute([$number, $hash, $type]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return !empty($row) ? $row : false;
}

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
  if (is_dir($src)) {
    //if (file_exists($dst)) { rrmdir($dst); } else { mkdir($dst); }
    $files = scandir($src);
    foreach ($files as $file)
    if ($file != "." && $file != "..") rcopy("$src/$file", "$dst/$file"); 
  }
  else if (file_exists($src)) copy($src, $dst);
}



// отклоняем запрос при неправильном json PHP>=8.3.0
//if( !json_validate($_POST['JSON']) ) { die('invalid json') }
$event = json_decode($_POST['JSON']); 
if ( json_last_error() != JSON_ERROR_NONE) { die(json_last_error_msg()); }
// реквизиты соревнования
$COMPETITION_NAME = $event->name;
$TYPE = $event->type_id;      //это создаваемый тип соревнований, для фильтра списка
$DATE_FROM = $event->date_start;
$DATE_TO = $event->date_end;
$PLACE = $event->location;
$RANK = $event->events;
$THEME = $event->theme;
$PAGE_LOGO_BASE64CODE = $event->title_img;
if (!is_null($PAGE_LOGO_BASE64CODE)) $PAGE_LOGO_MIME = $event->title_mime;


////////////////////////////////////////
// авторизация по $LICENSE_HASH и $LICENSE_NUMBER
// check POST variables
$HASH = (ctype_xdigit($_POST["HASH"])) ? $_POST["HASH"] : 0;
$guid_regex = "/^(?:\\{{0,1}(?:[0-9a-fA-F]){8}-(?:[0-9a-fA-F]){4}-(?:[0-9a-fA-F]){4}-(?:[0-9a-fA-F]){4}-(?:[0-9a-fA-F]){12}\\}{0,1})$/"; 
$NUMBER = (is_string($_POST["NUMBER"]) || preg_match($guid_regex, $_POST["NUMBER"])) ? $_POST["NUMBER"] : 0;   //UUIDv4
$License = Licence_Validation($NUMBER,$HASH,$TYPE);
if (!$License) die('Error licence validation or Incompatible type!');
else $LicID = $License['LICENCE_ID'];

//блокируем повторный запрос создания такого же event
//$query="SELECT * FROM COMPETITION WHERE LICENCE_ID = ? AND DESCRIPTION->'$.id' = ?";            //MySQL
$query="SELECT * FROM COMPETITION WHERE LICENCE_ID = ? AND JSON_VALUE(DESCRIPTION, '$.id') = ?";  //MariaDB
$stmt = db()->prepare($query);
$stmt->execute([$LicID, $event->id]); 
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($row)) { die('Event already exists!'); }
////////////////////////////////////////


//получить ID мероприятия
$row = db()->query("SELECT NEXTVAL(event_sequence);")->fetch();
$COMPETITION_ID = $row[0];     // COMPETITION_ID
$D = EVENTS_DIR.$COMPETITION_ID.'/';

//папка соревнования
rrmdir($D);            // на всякий случай удаляем все 
if (!mkdir($D, 0777, true))  die('Failed to create directories...'); 
//папки для каждого зачета
foreach ($RANK as $value) {
  $event_dir = $D.get_object_vars($value)['event_id'].'/';
  if (!mkdir($event_dir, 0777, true))  die('Failed to create directories...'); 
  //папки для данных
  if (!mkdir($event_dir."divepool/", 0777, true))  die('Failed to create directories...');
  if (!mkdir($event_dir."team/", 0777, true))  die('Failed to create directories...');
  if (!mkdir($event_dir."detail/", 0777, true))  die('Failed to create directories...');
  if (!mkdir($event_dir."video/", 0777, true))  die('Failed to create directories...');
}
// скопировать из шаблона все файлы в папку event'а
rcopy(EVENT_TEMPLATE_DIR, $D);


//картинка шапки страницы
if (!is_null($PAGE_LOGO_BASE64CODE)) {
  $imageData = base64_decode($PAGE_LOGO_BASE64CODE);
  $imageInfo = getimagesizefromstring($imageData);
  if ($imageInfo !== false) {
    $imageType = $imageInfo[2]; // Индекс 2 содержит тип изображения
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $PAGE_LOGO_IMAGE = "PAGE_LOGO.JPEG";
            break;
        case IMAGETYPE_PNG:
            $PAGE_LOGO_IMAGE = "PAGE_LOGO.PNG";
            break;
        case IMAGETYPE_BMP:
             $PAGE_LOGO_IMAGE = "PAGE_LOGO.BMP";
             break;
        case IMAGETYPE_GIF:
            $PAGE_LOGO_IMAGE = "PAGE_LOGO.GIF";
            break;
        case IMAGETYPE_WEBP:
            $PAGE_LOGO_IMAGE = "PAGE_LOGO.WebP";
            break;
        default:
            $PAGE_LOGO_IMAGE = ""; //Неизвестный тип изображения
    }
  } else {
    $PAGE_LOGO_IMAGE = 'PAGE_LOGO.'.$PAGE_LOGO_MIME;  //берем данные из json
    echo "Ошибка: Не удалось определить тип изображения";
  }
} else {
  $PAGE_LOGO_IMAGE = "";
}
if ($PAGE_LOGO_BASE64CODE != '') file_put_contents($D.$PAGE_LOGO_IMAGE, $imageData); //header image



//создать html страницу event из шаблона
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
$smarty->assign("BASEURL", "http://{$_SERVER['SERVER_NAME']}/".$D);
$smarty->assign("THEME", (isset($THEME) && in_array($THEME,['dark','light'])) ? $THEME : "light");
//$smarty->assign("PAGE_LOGO_MIME", $PAGE_LOGO_MIME);
//$smarty->assign("PAGE_LOGO_BASE64CODE", $PAGE_LOGO_BASE64CODE); //не применишь, т.к. максимальный размер для css - 64k
$smarty->assign("PAGE_LOGO", $PAGE_LOGO_IMAGE);
$output = $smarty->fetch('index.tpl');					//шаблон страницы соревнований

file_put_contents($D.'index.html',$output);	// write to file event в папку COMPETITION_ID

//сохранить исходный json в папке event'а
if (file_put_contents($D.'event.json', json_encode($event,JSON_PRETTY_PRINT)));
unset($event['title_img']); //не хранить image в БД, т.к. уже сохранена в $PAGE_LOGO_IMAGE

//если все ОК - добавить мероприятие в список
$stmt= db()->prepare("INSERT INTO COMPETITION (COMPETITION_ID, DESCRIPTION,LICENCE_ID) VALUES(?,?,?)")->execute([$COMPETITION_ID,json_encode($event), $LicID]);

// вернуть full path URL папки мероприятия
echo "http://{$_SERVER['SERVER_NAME']}/{$D}";
// или вернуть только ID созданного мероприятия
//echo $COMPETITION_ID;


?>