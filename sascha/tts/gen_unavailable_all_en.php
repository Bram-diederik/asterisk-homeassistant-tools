#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";
$greet = greet();
$failbackmessage = "Hello. Sascha Speaking Bram is unavailable at this moment.";
$message = "$greet, $name. Sascha Speaking Bram is unavailable at this moment.";

$failbackfile = tts($failbackmessage);
if ($file = tts($message)) {
#  file_put_contents("/opt/sascha/msg",$file);
  echo $file;
} else {
  echo $failbackfile;
}
?>


