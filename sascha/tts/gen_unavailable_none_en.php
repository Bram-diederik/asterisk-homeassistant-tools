#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$failbackmessage = "Hello. Sascha Speaking the one you want to call is unavailable at this moment.";
$message = "$greet, Sascha Speaking the one you want to call is unavailable at this moment.";

$failbackfile = tts($failbackmessage);
if ($file = tts($message)) {
#  file_put_contents("/opt/sascha/msg",$file);
  echo $file;
} else {
  echo $failbackfile;
}
?>


