#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$busy = busy_msg();
$name = caller();

$failbackmessage = "Hallo dit is Sascha. Bram is bezig.";
$message = "$greet, $name. dit is Sascha. Bram is bezig, $busy.";

$failbackfile = tts($failbackmessage,"nl");
if ($file = tts($message,"nl")) 
  echo $file;
else 
  echo $failbackfile;
?>
