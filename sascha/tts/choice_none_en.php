#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";
$message = "Press 1 for voicemail 2 for a call with utmost importance or password for entry";
if ($file = tts($message))
  echo $file;
?>
