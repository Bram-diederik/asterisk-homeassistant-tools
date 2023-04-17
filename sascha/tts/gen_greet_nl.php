#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();

$failbackmessage = "Hallo";
$message = "$greet";

$failbackfile = tts($failbackmessage,"nl");
if ($file = tts($message,"nl")) 
  echo $file;
else 
  echo $failbackfile;
?>
