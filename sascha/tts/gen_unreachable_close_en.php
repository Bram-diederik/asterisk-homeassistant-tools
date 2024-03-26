#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";
$greet = greet();
$msg = offline_msg();


$failbackmessage = "Hello. Sascha Speaking Bram is not home at this moment and his mobile phone is unreachable";
$message = "$greet, $name. Sascha Speaking Bram is not home at this moment and his mobile phone is unreachable $msg ";

$failbackfile = tts($failbackmessage);
if ($file = tts($message)) {
#  file_put_contents("/opt/sascha/msg",$file);
  echo $file;
} else {
  echo $failbackfile;
}
?>


