<?php

/*
$mysqli = mysqli_connect("localhost", "root", "", "optimus");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// check POST variables
$NUMBER = ( isset($_POST["NUMBER"]) || !ctype_xdigit($_POST["NUMBER"])) ? $_POST["NUMBER"] : 0;
if (!isset($_POST["NUMBER"]) || !ctype_xdigit($_POST["HASH"])) { die ('Incorrect hash'); } 
$HASH = $_POST["HASH"];
$EVENT_ID = $_POST["EVENTI_ID"];


//Licence_Validation
$query = "SELECT co.LICENCE_ID, co.DESCRIPTION->'$.id' EVENT_ID FROM COMPETITION co LEFT JOIN LICENCE li ON li.LICENCE_ID=co.LICENCE_ID" // standard
  ." WHERE ACTIVE=true AND LICENCETYPE=1 AND NOW() BETWEEN DATESTART AND DATEEND"	//дата в диапазоне срока действия лицензии
  ." AND NUMBER='{$NUMBER}'"
  ." AND LICENCEHASH = '{$HASH}'"		//сверяем md5hash
  ." AND (EVENTTYPES & (1 << {$TYPE})) = 1"	//входит ли дисциплина в список лицензии 
  ." AND EVENT_ID = {$EVENT_ID}";
$result = mysqli_query($mysqli, $query); 
$row = mysqli_fetch_assoc($result);
if ( is_null($row['LICENCE_ID']) ) die('Error licence validation or Incompatible type!');
list($LicID, $EventID) = $row;
*/

$EventID=0;
// определяем $target_dir куда копировать файлы
$target_dir = "events/{$EventID}/";
$target_file = $target_dir . basename($_FILES["FILE"]["name"]);
echo $target_file;
$uploadOk = 1;
/*
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if (!in_array($imageFileType, ["jpg","png","jpeg","gif"])) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}
*/
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
echo 'Copying '.$target_file;
  if (move_uploaded_file($_FILES["FILE"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars( basename( $_FILES["FILE"]["name"])). " has been uploaded.";
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}
?>
