#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$name = caller();

$failbackmessage = "Hallo dit is Sascha. Bram is bezig.";
$message = "$greet $name dit is Sascha. Bram is bezig.";

$failbackfile = tts($failbackmessage,"nl");
if ($file = tts($message,"nl")) {
#  file_put_contents("/opt/sascha/msg",$file);
  echo $file;
} else {
#  file_put_contents("/opt/sascha/msg",$failbackfile);
  echo $failbackfile;
}
?>
