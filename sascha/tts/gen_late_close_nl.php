#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$name = caller();

$message = "$greet, $name. Dit is Sascha. Het is laat en Bram slaapt waarschijnlijk.";
$failbackmessage = "Hallo, Dit is Sascha. Het is laat en Bram wilt niet gestoord worden op dit tijd stip.";

$failbackfile = tts($failbackmessage);
if ($file = tts($message)) 
  echo $file;
else 
  echo $failbackfile;
?>
