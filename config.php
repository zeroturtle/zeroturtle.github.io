<?php

try {
    //$pdo = new PDO('sqlite:optimus.db'); //sqlite
    $pdo = new PDO("mysql:host=localhost;dbname=optimus", 'root', ''); //mysql
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>