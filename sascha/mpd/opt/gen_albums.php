#!/usr/bin/php

<?php
$host = "192.168.5.2";
$sPlaylists = shell_exec("mpc --host $host ls");


$itemsToRemove = array("dance", "looneytoons", "slaaptracks");
$aPlaylists = explode("\n",$sPlaylists);
$aPlaylists = array_diff($aPlaylists, $itemsToRemove);



#FULL Playlists
foreach($aPlaylists as $sPlaylist) {
  $sPlaylist = trim($sPlaylist);
  if ($sPlaylist ==="" ) {
    //donothing
  }
   elseif (strpos($sPlaylist, "mpddir_") === 0) {
    //do nothing
  }
  else if (strpos($sPlaylist, "mpdalbum_") === 0) {
    //do nothing
  } else {
         ///dostuff
        $artist = $sPlaylist;
        echo "#########################\n$artist\n";


        $albums = shell_exec("mpc --host $host search albumartist \"$artist\"");


        $albumArray = explode("\n", $albums);
        print_r($albumArray);

  }
}

?>
