#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "en";
$message = "Dear Caller. You are blacklisted. press 1 to file a complaint. Here is a quote of the day to feel better.";
if ($file = tts($message))
  echo $file;
?>
