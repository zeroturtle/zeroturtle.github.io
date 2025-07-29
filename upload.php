<?php
// Include config file
require_once "config.php";

define('EVENTS_DIR', 'events/');

function Licence_Validation($number,$hash) {
  global $pdo;
  $query = "SELECT * FROM LICENCE WHERE ACTIVE=true" 
    ." AND NOW() BETWEEN DATESTART AND DATEEND"	//дата в диапазоне срока действия лицензии
    ." AND LICENCETYPE=1"			//только для Site типа
    ." AND NUMBER = ?"				//номер лицензии
    ." AND LICENCEHASH = ?";			//сверяем md5hash
  $stmt = $pdo->prepare($query);
  $stmt->execute([$number, $hash]);
  $row = $stmt->fetch();
  return isset($row) ? $row : false;
}

//считать из json параметры загрузки файла
// формат json: {comp_id="int", event_id="string", resource_name="string", filename="string", file="...base64code..."}
$file = json_decode($_POST['JSON']); 
if ( json_last_error() != JSON_ERROR_NONE) { die(json_last_error_msg()); }
$compID = $file->comp_id;
$event_id = $file->event_id;
$resource_id = $file->resource;


// check POST variables
$NUMBER = ( isset($_POST["NUMBER"]) || !ctype_xdigit($_POST["NUMBER"])) ? $_POST["NUMBER"] : 0;
if (!isset($_POST["NUMBER"]) || !ctype_xdigit($_POST["HASH"])) { die ('Incorrect hash'); } 
$HASH = $_POST["HASH"];
$HASH = 'c51ef7bd8dfcdf7d76fde411f1dca228';
$NUMBER = '190051E7-F81D-4068-B78C-BD8CDE9A';
$License = Licence_Validation($NUMBER,$HASH);
if (!$License) die('Error licence validation or Incompatible type!');
else $LicID = $License['LICENCE_ID'];

//загружать данные только с той же лицензией, кем создано 
$query="SELECT * FROM COMPETITION WHERE LICENCE_ID = ? AND JSON_VALUE(DESCRIPTION, '$.id') = ?";  //MariaDB
$stmt = $pdo->prepare($query);
$stmt->execute([$LicID, $compID]); 
$row = $stmt->fetch();
if (!isset($row)) die('You should use identical licence to upload data!'); 
else $compID = $row['COMPETITION_ID']; 



// определяем $target_dir куда копировать файлы
$comp_dir = EVENTS_DIR.$compID.'/';
$event_dir = $comp_dir.'/'.$event_id.'/';
$resource_name = ['logo', 'proto', 'divepool', 'team', 'detail', 'video'];
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
