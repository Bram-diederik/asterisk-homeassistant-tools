#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();

$failbackmessage = "Hallo. toets 1 voor nieuws.";
$message = "$greet, toets 1 voor nieuws.";

$failbackfile = tts($failbackmessage,"nl");
if ($file = tts($message,"nl")) 
  echo $file;
else 
  echo $failbackfile;
?>
