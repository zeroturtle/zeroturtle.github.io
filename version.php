<?php

function array_keys_to_lowercase($array) {
  $new_array = [];
  foreach ($array as $key => $value) {
    $new_key = strtolower($key);
    if (is_array($value)) {
        $new_array[$new_key] = array_keys_to_lowercase($value);
    } else {
        $new_array[$new_key] = $value;
    }
  }
  return $new_array;
}

$AppName = strtolower($_GET['app']);  

$JSONdata = json_decode(file_get_contents('version.info')); 
if ( json_last_error() != JSON_ERROR_NONE) { die('Error: Could not parse version info: '.json_last_error_msg()); }

foreach($JSONdata as $product_info) {
  if (strtolower($product_info->name) == $AppName) {
    try {
      $jsonString = json_encode($product_info, JSON_PRETTY_PRINT); //JSON_THROW_ON_ERROR
      header('Content-Type: application/json');
      echo $jsonString;
    } catch (JsonException $e) {
      echo "JSON encoding error: " . $e->getMessage();
    }
    break;
  }
}



/*
//название модуля без учета регистра
function FindKey($key, $array){
  $lowerArr = array_change_key_case($array);
  return in_array($key, array_keys($lowerArr)) ? $lowerArr[$key] : null;
}

$AppName = strtolower($_GET['app']);  

//версия для ini-файла
$ini = parse_ini_file("version.info", true);
if ($ini !== false) {
  header('Content-Type: application/json');
  try {
    $version['version'] = FindKey($AppName, $ini['Apps']);
    $version['MD5'] = FindKey($AppName, $ini['Check']);
    $jsonString = json_encode($version, JSON_PRETTY_PRINT); //JSON_THROW_ON_ERROR
    echo $jsonString;
  } catch (JsonException $e) {
    echo "JSON encoding error: " . $e->getMessage();
  }
} else {
  echo "Error: Could not parse version.info" . PHP_EOL;
}
*/

?>