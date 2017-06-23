<?php // captcha.php

session_start(); 
unset($_SESSION['captcha']); 

function randomString($len) { 
  function make_seed(){ 
     list($usec , $sec) = explode (' ', microtime()); 
     return (float) $sec + ((float) $usec * 100000); 
  } 
  srand(make_seed());  
  //Der String $possible enthält alle Zeichen, die verwendet werden sollen 
  //$possible="2345689RaAHQeBb?!"; 
  $possible="2345689bwcadu"; 
  $str=""; 
  while(strlen($str)<$len) { 
    $str.=substr($possible,(rand()%(strlen($possible))),1); 
  } 
return($str); 
} 

$text = randomString(5);  //Die Zahl bestimmt die Anzahl stellen 
$_SESSION['captcha'] = $text; 
      
header('Content-type: image/png'); 
$img = ImageCreateFromPNG('captcha.png'); //Backgroundimage 
$color = ImageColorAllocate($img, 0, 0, 0); //Farbe 
putenv('GDFONTPATH=' . realpath('.'));
$ttf = "espkey";
$t_x = -5; 
for ($i = 0; $i<5; $i++) {
$ttfsize = rand(24,28); //Schriftgrösse 
 $angle = rand(-15,15); 
 $t_x += 20;
 $t_y = rand(20,28); 
 $letter = substr($text,$i,1);
 imagettftext($img, $ttfsize, $angle, $t_x, $t_y, $color, $ttf, $letter); 
}
imagepng($img); 
imagedestroy($img); 

?> 
