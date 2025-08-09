<?php
$Price = 0;

//базовая цена
switch($_POST['licence']) {
  case 0: $RegularPrice = 0;
          break;
  case 1: $RegularPrice = 200;
          break;
  default: $RegularPrice = 200;
}

//считаем скидку за предоплату 
$Period = 1; //isset($_POST['years']) ? intval($_POST['years']) : 1;
if (isset($_POST['types'])) 
  for ($i=0; $i<count($_POST['types']); $i++) {
    for ($y=1; $y<=$Period; $y++) {
      switch($y){
        case 1: $Discount = 0;
                break;
        case 2: $Discount = 10;
                break;
        case 3: $Discount = 25;
                break;
        default: $Discount = 0;
      }
      $Price += $RegularPrice * (1 - $Discount/100);
    }
  }

echo $Price;


?>