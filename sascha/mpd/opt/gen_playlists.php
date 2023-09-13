#!/usr/bin/php

<?php
$host = "192.168.5.2";
$sPlaylists = shell_exec("mpc --host $host ls");

$aPlaylists = explode("\n",$sPlaylists);


#FULL Playlists
foreach($aPlaylists as $sPlaylist) {
 if (trim($sPlaylist) != "") {
      if (strpos($sPlaylist, "mpddir_") === 0) {
            exec("mpc --host $host rm \"$sPlaylist\"");
            continue; // Skip further processing for this playlist
        }
   exec("mpc --host $host clear");
   exec("mpc --host $host rm \"$sPlaylist\"");
   exec("mpc --host $host add \"$sPlaylist\"",$output,$status); 
   exec("mpc --host $host shuffle"); 
   exec("mpc --host $host save \"mpddir_$sPlaylist\"");
  }
}

?>
