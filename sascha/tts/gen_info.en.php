#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";

$message = file_get_contents("/opt/sascha/asterisk/written_status_en.txt");
$failbackmessage = "Sorry, there is no infomation available";
$failbackfile = tts($failbackmessage);
if ($file = tts($message))
  echo $file;
else 
  echo $failbackfile;
?>
