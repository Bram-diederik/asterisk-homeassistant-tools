#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";

$message = file_get_contents("/opt/HA/written_status_nl");
$failbackmessage = "Sorry, er is geen informatie op dit ogenblik";
$failbackfile = tts($failbackmessage);
if ($file = tts($message))
  echo $file;
else 
  echo $failbackfile;
?>
