#!/usr/bin/php

<?php
$mpd_host = "192.168.5.40";
$extention = "";

$header = '

[music_playlists]
exten => select,1,Background(${SHELL(/opt/sascha/gentts.php nl "toets 2 voor a b c toets 3 voor d e f toets 4 voor g h i toets 5 voor j k l toets 6 voor m n o toets 7 voor p q r s toets 8 voor t u v toets 9 voor w x y z toets 0 om terug te gaan")})
exten => select,2,WaitExten(30)
exten => select,3,Hangup();

exten => 2,1,Goto(music_playlists_2,select,1)
exten => 2,2,Hangup();

exten => 3,1,Goto(music_playlists_3,select,1)
exten => 3,2,Hangup();

exten => 4,1,Goto(music_playlists_4,select,1)
exten => 4,2,Hangup();

exten => 5,1,Goto(music_playlists_5,select,1)
exten => 5,2,Hangup();

exten => 6,1,Goto(music_playlists_6,select,1)
exten => 6,2,Hangup();

exten => 7,1,Goto(music_playlists_7,select,1)
exten => 7,2,Hangup();

exten => 8,1,Goto(music_playlists_8,select,1)
exten => 8,2,Hangup();

exten => 9,1,Goto(music_playlists_9,select,1)
exten => 9,2,Hangup();

exten => e,1,Goto(select,1)
';

echo $header;

$sPlaylists = shell_exec("mpc --host $mpd_host ls");

$aPlaylists = explode("\n",$sPlaylists);
$aPlaylists = array_unique($aPlaylists );
$select = 0;
$count = 0;

foreach($aPlaylists as $sPlaylist) {
   $sPlaylist = trim($sPlaylist); 
   if ($sPlaylist != "") { 
      switch(strtolower(substr($sPlaylist, 0,1))) {
        case "a":
        case "b":
        case "c":
           if ($select == "2") {
             $count++;
           } else {
              $count = 0;
              $select = "2";
           } 
            $subPlaylist[$select][$count] = $sPlaylist; 
           break;
        case "d":
        case "e":
        case "f":
           if ($select == "3") {
             $count++;
           } else {
              $count = 0;
              $select = "3";
           } 
           $subPlaylist[$select][$count] = $sPlaylist; 
           break;
        case "g":
        case "h":
        case "i":
           if ($select == "4") {
             $count++;
           } else {
              $count = 0;
              $select = "4";
           } 
           $subPlaylist[$select][$count] = $sPlaylist; 
           break;
        case "j":
        case "k":
        case "l":
           if ($select == "5") {
             $count++;
           } else {
              $count = 0;
              $select = "5";
           } 
           $subPlaylist[$select][$count] = $sPlaylist; 
           break;
        case "m":
        case "n":
        case "o":
           if ($select == "6") {
             $count++;
           } else {
              $count = 0;
              $select = "6";
           } 
            $subPlaylist[$select][$count] = $sPlaylist; 
           break;
        case "p":
        case "q":
        case "r":
        case "s":
           if ($select == "7") {
             $count++;
           } else {
              $count = 0;
              $select = "7";
           } 
            $subPlaylist[$select][$count] = $sPlaylist; 
           break;
        case "t":
        case "u":
        case "v":
           if ($select == "8") {
             $count++;
           } else {
              $count = 0;
              $select = "8";
           } 
            $subPlaylist[$select][$count] = $sPlaylist; 
           break;
        case "w":
        case "x":
        case "y":
        case "z":
           if ($select == "9") {
             $count++;
           } else {
              $count = 0;
              $select = "9";
           } 
            $subPlaylist[$select][$count] = $sPlaylist; 
           echo $sPlaylist;
           break;
     }
  }
}

foreach($subPlaylist as $key => $sub) {
   $i = 0;
   $subMessage  = "";
   $subExtentionBody = "";
     foreach($sub as  $subplaylist) {
#     echo "$key $subplaylist";
     $i++;
     $subMessage .= "toets $i voor $subplaylist ";
     $subExtentionBody .= '
exten => '.$i.',1,Log(NOTICE,Playlist '.$subplaylist.')
exten => '.$i.',2,System(/opt/sascha/mpd/play_dir.sh "'.$subplaylist.'")
exten => '.$i.',3,System(/opt/sascha/snapcast/switch_slaapkamer.php mpd)
exten => '.$i.',4,Background(${SHELL(sleep 1 && /opt/sascha/tts/gen_mpd_playing.php)})
exten => '.$i.',5,Hangup();

';
   }
   $subMessage .= "toets 0 om terug te gaan";
   $subExtention = '
[music_playlists_'.$key.']
exten => select,1,Background(${SHELL(/opt/sascha/gentts.php nl "'.$subMessage.'")})
exten => select,2,WaitExten(30)
exten => select,3,Hangup();
'.$subExtentionBody.'
exten => 0,1,Goto(music_playlists)
exten => 0,2,Hangup()

exten => e,1,Goto(select,1)
';

   echo $subExtention;

}
?>
