#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";
$message = "Press 1 for voicemail or 2 to call Bram anyway or 3 for more information.";
if ($file = tts($message))
  echo $file;
?>
