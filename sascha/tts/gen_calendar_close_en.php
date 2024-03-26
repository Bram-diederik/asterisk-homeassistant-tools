#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";

$greet = greet();
$name = caller();
$cal = calendar("en");

$failbackmessage = "Hello. Sascha Speaking Bram is having an appointment.";
$message = "$greet, $name. Sascha Speaking Bram is having an appointment $cal.";
$failbackfile = tts($failbackmessage);
if ($file = tts($message))
  echo $file;
else 
  echo $failbackfile;
?>
