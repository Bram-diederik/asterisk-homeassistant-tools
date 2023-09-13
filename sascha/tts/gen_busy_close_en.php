#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";
$greet = greet();
$busy = busy_msg("en");
$name = caller();

$failbackmessage = "Hello. Sascha Speaking Bram is busy.";
$message = "$greet, $name. Sascha Speaking Bram is busy, $busy.";
$failbackfile = tts($failbackmessage);
if ($file = tts($message))
  echo $file;
else 
  echo $failbackfile;
?>
