<?php

require_once __DIR__ . '/auth/config/database.php';
require_once __DIR__ . '/auth/src/libs/connection.php';

mb_internal_encoding('UTF-8');


define('EVENTS_DIR', 'events/');
$resource_name = ['logo', 'proto', 'divepool', 'team', 'detail', 'video'];


function Licence_Validation($number,$hash) {
  $query = "SELECT * FROM LICENCE WHERE ACTIVE=true" 
    ." AND NOW() BETWEEN DATESTART AND DATEEND"	//дата в диапазоне срока действия лицензии
//    ." AND LICENCETYPE=1"			//только для Site типа
    ." AND NUMBER = ?"				//номер лицензии
    ." AND LICENCEHASH = ?";			//сверяем md5hash
//    ." AND (EVENTTYPES & (1 << ?)) <> 0";	//тип соревнования входит в список дисциплин лицензии
  $stmt = db()->prepare($query);
  $stmt->execute([$number, $hash]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return !empty($row) ? $row : false;
}

//считать из json параметры загрузки файла
// формат json: {comp_id="int", event_id="string", resource_name="string", filename="string", file="base64code(image)"}
$file = json_decode($_POST['JSON']); 
if ( json_last_error() != JSON_ERROR_NONE) { die(json_last_error_msg()); }
$compID = $file->comp_id;
$event_id = $file->event_id;
$resource_id = $file->resource;

//авторизация
// check POST variables
$HASH = (ctype_xdigit($_POST["HASH"])) ? $_POST["HASH"] : 0;
$guid_regex = "/^(?:\\{{0,1}(?:[0-9a-fA-F]){8}-(?:[0-9a-fA-F]){4}-(?:[0-9a-fA-F]){4}-(?:[0-9a-fA-F]){4}-(?:[0-9a-fA-F]){12}\\}{0,1})$/"; 
$NUMBER = (is_string($_POST["NUMBER"]) || preg_match($guid_regex, $_POST["NUMBER"])) ? $_POST["NUMBER"] : 0;   //UUIDv4
$License = Licence_Validation($NUMBER,$HASH);
if (!$License) die('Error licence validation or Incompatible type!');
else $LicID = $License['LICENCE_ID'];

//разрешить загружать данные только той же лицензии, кем создано 
$query="SELECT * FROM COMPETITION WHERE LICENCE_ID = ? AND JSON_VALUE(DESCRIPTION, '$.id') = ?";  //MariaDB
$stmt = db()->prepare($query);
$stmt->execute([$LicID, $compID]); 
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($row)) die('You should use identical licence to upload data!'); 
else $compID = $row['COMPETITION_ID']; //ID мероприятия


// определяем $target_dir куда копировать файлы
$comp_dir = EVENTS_DIR.$compID.'/';
$event_dir = $comp_dir.$event_id.'/';
switch(array_search($resource_id, $resource_name)) {
  case 0: $target_dir =  $comp_dir;
    break;
  case 1: $target_dir =  $event_dir;
    break;
  case 2: $target_dir =  $event_dir."divepool/";
    break;
  case 3: $target_dir =  $event_dir."team/";
    break;
  case 5: $target_dir =  $event_dir."detail/";
    break;
  case 6: $target_dir =  $event_dir."video/";
    break;
  default: die('Unexpected destination!');
}
$imageData = base64_decode($file->file);
/*
$imageInfo = getimagesizefromstring($imageData);
if ($imageInfo !== false) {
    $imageType = $imageInfo[2]; // Индекс 2 содержит тип изображения
    $mimeType = $imageInfo['mime']; // Также можно получить mime-тип

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            echo "Тип изображения: JPEG";
            break;
        case IMAGETYPE_PNG:
            echo "Тип изображения: PNG";
            break;
        case IMAGETYPE_GIF:
            echo "Тип изображения: GIF";
            break;
        case IMAGETYPE_BMP:
             echo "Тип изображения: BMP";
             break;
        case IMAGETYPE_WEBP:
            echo "Тип изображения: WebP";
            break;
        default:
            echo "Неизвестный тип изображения";
    }
    echo "MIME-тип: " . $mimeType;
} else {
    echo "Ошибка: Не удалось определить тип изображения";
}
*/
file_put_contents($target_dir.$file->name, $imageData);
?>
