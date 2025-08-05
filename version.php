<?php

//название модуля без учета регистра
function FindKey($key, $array){
  $lowerArr = array_change_key_case($array);
  return in_array($key, array_keys($lowerArr)) ? $lowerArr[$key] : null;
}

$AppName = strtolower($_GET['app']);  
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
?>