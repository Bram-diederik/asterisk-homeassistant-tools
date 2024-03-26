#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$name = caller();

$message = "Toets 1 voor voicemail, 2 voor een belangrijk gesprek of wachtwoord voor toegang.";

if ($file = tts($message,"nl")) {
#  file_put_contents("/opt/sascha/msg",$file);
  echo $file;
}
?>
