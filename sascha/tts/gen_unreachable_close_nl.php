#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$name = caller();

$msg = offline_msg();


$failbackmessage = "Hallo dit is Sascha. Bram is niet thuis op dit moment en zijn mobiele telefoon is onbereikbaar.";
$message = "$greet $name dit is Sascha. Bram is niet thuis op dit moment en zijn mobiele telefoon is onbereikbaar. $msg";


$failbackfile = tts($failbackmessage,"nl");
if ($file = tts($message,"nl")) {
#  file_put_contents("/opt/sascha/msg",$file);
  echo $file;
} else {
#  file_put_contents("/opt/sascha/msg",$failbackfile);
  echo $failbackfile;
}
?>
