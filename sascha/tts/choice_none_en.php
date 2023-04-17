#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";
$message = "Press 1 for voicemail or password for entry";
if ($file = tts($message))
  echo $file;
?>
