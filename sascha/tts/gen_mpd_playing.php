#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$lang = "nl";
$greet = greet();
$name = caller();
$cal = calendar();
$output = '';
exec("mpc --host 192.168.5.2 | head -n 1 | sed 's/[()]//g' | sed 's/ ([0-9]\+:[0-9]\+)//'", $output);
$output2 = implode("\n",$output);
$failbackmessage = "oeps foutje bedankt";

$failbackfile = tts($failbackmessage);
if ($file = tts($output2))
  echo $file;
else 
  echo $failbackfile;
?>

