<?php
require_once __DIR__ . '/auth/config/database.php';
require_once __DIR__ . '/auth/src/libs/connection.php';


define('EVENTS_PER_PAGE', 8);
// дисциплины по группам
$TypeArr = [''=>[],'FS'=>[1,2,3],'AE'=>[4],'IS'=>[5,6],'DY'=>[10,11],'WS'=>[7,12],'CP'=>[8],'CF'=>[9],'SP'=>[13]]; 

//2. Проверяем фильтры
$F_Year = ( isset($_COOKIE["Year"]) && filter_var($_COOKIE["Year"],FILTER_VALIDATE_INT)) ? intval($_COOKIE["Year"]) : 0;
$F_Type = ( isset($_COOKIE["Type"]) && array_key_exists($_COOKIE["Type"],$TypeArr)) ? $_COOKIE["Type"] : '';
$F_Name = ( isset($_COOKIE["Name"]) && filter_var($_COOKIE["Name"], FILTER_SANITIZE_STRING)) ? ($_COOKIE["Name"]) : '';//mb_strtolower
$page   = ( isset($_GET['page']) && filter_var($_GET['page'],FILTER_VALIDATE_INT)) ? intval($_GET['page']) : 0;

// 3. запрашиваем список Events в json формате
$Events = [];
$sql = "SELECT COMPETITION_ID, DESCRIPTION FROM competition WHERE Visible=1"
// MySQL
/*	.(($F_Name != '') ? " AND (DESCRIPTION->'$.name' LIKE '%'||'$F_Name'||'%') OR (DESCRIPTION->'$.location' LIKE '%'||'$F_Name'||'%')" : '')
	.(($F_Year != 0)  ? " AND YEAR(json_unquote(DESCRIPTION->'$.date_start')) = '$F_Year' " : '')  //работает для MySQL>=8.0
	.(($F_Type != '') ? " AND DESCRIPTION->$.type_id IN (".implode(',',$TypeArr[$F_Type]).")" : '')
*/
//MariaDB
    .(($F_Name != '') ? " AND ( UPPER(JSON_VALUE(DESCRIPTION, '$.name')) LIKE UPPER(CONCAT('%','$F_Name','%')) OR UPPER(JSON_VALUE(DESCRIPTION, '$.location')) LIKE UPPER(CONCAT('%','$F_Name','%')) )" : '')
    .(($F_Year != 0)  ? " AND YEAR(JSON_VALUE(DESCRIPTION, '$.date_start')) = '$F_Year' " : '')  //работает для MySQL>=8.0
    .(($F_Type != '') ? " AND JSON_VALUE(DESCRIPTION, '$.type_id') IN (".implode(',',$TypeArr[$F_Type]).")" : '')
    ." ORDER BY COMPETITION_ID DESC LIMIT ".EVENTS_PER_PAGE." OFFSET ".($page)*EVENTS_PER_PAGE;

  $statement = db()->query($sql);
  while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $Events[' '.$row['COMPETITION_ID']] = json_decode($row['DESCRIPTION'], true);     // пробел нужен чтоб исключить сортировку объекта
  }
echo json_encode($Events);	//вывести json объект, где ключи -это _COMPETITION_ID_, а значения event-объект json

$pdo = null; 
?>