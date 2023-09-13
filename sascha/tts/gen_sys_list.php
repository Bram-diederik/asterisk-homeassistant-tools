#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$name = caller();

$message = "$greet, $name, toets 1 om voicemail te luisteren 2 om wekker in te stellen";
$failbackmessage = "$toets 1 om voicemail te luisteren 2 om wekker in te stellen";

$failbackfile = tts($failbackmessage);
if ($file = tts($message)) 
  echo $file;
else 
  echo $failbackfile;
?>
