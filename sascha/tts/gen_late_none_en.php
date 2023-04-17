#!/usr/bin/env php
<?php
include("/opt/sascha/common.php");
$lang = "en";
$greet = greet();
$busy = busy_msg();
$name = caller();

$failbackmessage = "Hello. Sascha Speaking It is late and the person you want to call does not want to be disturbed.";
$message = "$greet, $name. Sascha Speaking It is late and the person you want to call does not want to be disturbed.";
$failbackfile = tts($failbackmessage);
if ($file = tts($message))
  echo $file;
else 
  echo $failbackfile;
?>
