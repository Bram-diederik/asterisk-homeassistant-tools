#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$name = caller();
$cal = calendar();
$message = "$greet $name dit is Sascha. Bram heeft een afspraak.";
$failbackmessage = "Hallo, Dit is Sascha. Bram heeft een afspraak.";

$failbackfile = tts($failbackmessage);
if ($file = tts($message))
  echo $file;
else 
  echo $failbackfile;
?>
