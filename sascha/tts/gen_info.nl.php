#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";

$message = file_get_contents("/opt/sascha/asterisk/written_status_nl.txt");
$failbackmessage = "Sorry, er is geen informatie op dit ogenblik";
$failbackfile = tts($failbackmessage);
if ($file = tts($message))
  echo $file;
else 
  echo $failbackfile;
?>
