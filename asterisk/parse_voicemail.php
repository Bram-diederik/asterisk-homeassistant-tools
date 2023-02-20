#!/usr/bin/php
<?php

$http_path = "/var/www/html/asterisk/";
$http_url = "http://192.168.1.2/asterisk/";
$spool_path = "/var/spool/asterisk/voicemail/default/1000/INBOX/";
$voicemail_count_file ="/opt/asterisk/count_file.txt";

$nFound = 0;
if ($handle = opendir($spool_path)) {
    echo "Directory handle: $handle\n";
    echo "Entries:\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
#        echo "$entry\n";
       $pattern = "/msg(\d{4})\.wav/";
       if(preg_match_all($pattern, $entry, $matches)) {
           $voicemails[] = $matches[1][0];
           $nFound++;
          #print_r($matches);
       }

    }

    closedir($handle);
}

$last_count = file_get_contents($voicemail_count_file);

if ($last_count ==  $nFound ) {
  echo "no change found";
  exit(0);
}
if ($nFound > 0) {
    asort($voicemails);
    #print_r($voicemails);
   $voicemailCount = 0;
   $mp3Count = 0;
   unlink($http_path."/playlist.m3u");
   foreach ($voicemails as $voicemail) {
    $voicemailCount++;
    $mp3Count++;
    echo "$voicemail\n";
    $info = parse_ini_file($spool_path.'/msg'.$voicemail.'.txt');
    print_r($info);
    $name =  $info['callerid'];
    unlink($http_path."/part".$mp3Count.".mp3");
    system("/usr/local/bin/gtts-cli -l nl 'voicemail $voicemailCount van $name' >  ".$http_path."/part".$mp3Count.".mp3");
    file_put_contents($http_path."/playlist.m3u", $http_url."/part".$mp3Count.".mp3\n", FILE_APPEND | LOCK_EX);
    $mp3Count++;
    unlink($http_path."/part".$mp3Count.".mp3");
    system("ffmpeg -i ".$spool_path."/msg".$voicemail.".wav -ab 32k  -filter:a \"volume=3.5\"  ".$http_path."/part".$mp3Count.".mp3");
    file_put_contents($http_path."/playlist.m3u",$http_url."/part".$mp3Count.".mp3\n", FILE_APPEND | LOCK_EX);
   }
} else {
    foreach (new DirectoryIterator($http_path) as $fileInfo) {
      if(!$fileInfo->isDot()) {
          unlink($fileInfo->getPathname());
       }
    }
    system("/usr/local/bin/gtts-cli -l nl 'U heeft geen berichten' >  ".$http_path."/part1.mp3");
    file_put_contents($http_path."/playlist.m3u", $http_url."/part1.mp3\n",  LOCK_EX);
}

echo "## $nFound ##";
file_put_contents($voicemail_count_file,$nFound);
