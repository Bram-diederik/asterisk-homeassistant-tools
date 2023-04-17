#!/usr/bin/php
<?php

$sounds = "/usr/share/asterisk/sounds/en/";

$hour = $argv[1];
$min = $argv[2];

$file = $sounds."wektijd_".$hour."_".$min.".gsm";
if (!file_exists($file)) {
 System("gtts-cli -l nl 'U wordt gewekt om ".$hour." uur ".$min." een goede dag' | sox -t mp3 -  -r 8000 -c1 ".$file);
}
