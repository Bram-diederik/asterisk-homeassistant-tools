#!/usr/bin/php
<?php
include("/opt/sascha/common.php");

if (count($argv) < 2) {
  die("Usage: script.php <lang> <message>\n");
}

$lang = $argv[1];
$message = $argv[2];

if ($file = tts($message))
  echo $file;
?>
