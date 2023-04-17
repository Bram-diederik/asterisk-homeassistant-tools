#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$name = caller();

$message = "Toets 1 voor voicemail, 2 om toch Bram te bellen of 3 voor meer informatie..";

if ($file = tts($message,"nl")) {
  echo $file;
}
?>
