#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";

$greet = greet();
$busy = busy_msg();
$name = caller();
$cal = calendar();

$failbackmessage = "Hello. Sascha Speaking. Bram is having an appointment.";
$message = "$greet, $name. Sascha Speaking. Bram is having an appointment.";
$failbackfile = tts($failbackmessage);
if ($file = tts($message))
  echo $file;
else 
  echo $failbackfile;
?>
