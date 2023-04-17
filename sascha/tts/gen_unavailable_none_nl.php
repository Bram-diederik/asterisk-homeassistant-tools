#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$name = caller();


$failbackmessage = "Hallo dit is Sascha. De gene die u probeerd te bellen is onbereikbaar op dit moment.";
$message = "$greet dit is Sascha. De gene die u probeerd te bellen is onbereikbaar op dit moment.";

$failbackfile = tts($failbackmessage,"nl");
if ($file = tts($message,"nl")) {
#  file_put_contents("/opt/sascha/msg",$file);
  echo $file;
} else {
#  file_put_contents("/opt/sascha/msg",$failbackfile);
  echo $failbackfile;
}
?>
