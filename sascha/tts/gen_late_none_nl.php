#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$message = "$greet dit is Sascha. Het is laat en de door u gebelde persoon wilt niet gestoord worden op dit tijdstip.";
$failbackmessage = "Hallo, Dit is Sascha. Het is laat en de door u gebelde persoon wilt niet gestoord worden op dit tijd stip.";

$failbackfile = tts($failbackmessage);
if ($file = tts($message)) {
  echo $file;
else 
  echo $failbackfile;
?>
