#!/usr/bin/php

<?php
$host = "192.168.5.40";
$sPlaylists = shell_exec("mpc --host $host ls");

$aPlaylists = explode("\n",$sPlaylists);

foreach($aPlaylists as $sPlaylist) {
   exec("mpc --host $host clear");
   exec("mpc --host $host load \"$sPlaylist\"",$output,$status);
   // Check the success/failure status
   if ($status !== 0) {
      exec("mpc --host $host clear");
      exec("mpc --host $host add \"$sPlaylist\"",$output,$status); 
      if ($status === 0) {
        exec("mpc --host $host shuffle"); 
        exec("mpc --host $host save \"$sPlaylist\"");
      }
   }
}
?>
