#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";
$greet = greet();
$busy = busy_msg();
$name = caller();

$failbackmessage = "Hello. Sascha Speaking It is late and Bram is probably asleep.";
$message = "$greet, $name. Sascha Speaking It is late and Bram is probably asleep.";
$failbackfile = tts($failbackmessage);
if ($file = tts($message))
  echo $file;
else 
  echo $failbackfile;
?>
