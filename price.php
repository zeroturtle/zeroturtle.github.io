<?php

switch(isset($Type)?$Type:0) {
  case 0: $RegularPrice = 200;
          break;
  case 1: $RegularPrice = 0;
          break;
  default: $RegularPrice = 200;
}

$Discount = 0;

$Price = 0;
if (isset($_POST['types'])) 
  for ($i=0; $i<count($_POST['types']); $i++) {
    $Price += $RegularPrice * (1 - $Discount/100);
}

echo $Price;


?>