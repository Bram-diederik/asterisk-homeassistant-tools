<?php
include("/opt/sascha/settings.php");

$dir = "/usr/share/asterisk/sounds/en_US_f_Allison/";

function wget2gsm($url,$outfile) {
  global $dir;
  exec( "wget -q -O - $url  | sox -t mp3 -  -r 8000 -c1  ".$dir.$outfile.".gsm", $output, $retval);
  if ($retval == 0) {
    return $outfile;
  }  elseif (file_exists($dir.$outfile)) {
    return $outfile;
  } else return false; 
}

function tts($message) {
  global $lang;
  global $dir;
  $message = str_replace('\'',"",$message);
  $messageFile = "sascha-".str_replace(':',"_",(str_replace(',',"_",(str_replace("/","_",str_replace(" ","_",$message))))));
  
#echo $dir.$messageFile ;

 if (file_exists($dir.$messageFile.".gsm")) {
    return $messageFile;
  }
  @exec("gtts-cli -l $lang '$message' | sox -t mp3 -  -r 8000 -c1 ".$dir . $messageFile . ".gsm " , $output, $retval);
#  @exec("gtts-cli -l $lang '$message' 2>/dev/null | sox -t mp3 -  -r 8000 -c1 ".$dir . $messageFile . ".gsm 2>/dev/null" , $output, $retval);
  if ($retval == 0) {
   return $messageFile;


  } else return false; 
}

function status() {
  global $lang;
  if ($lang = "nl") {
    return file_get_contents("/opt/HA/written_status_nl");
  } else {
    return file_get_contents("/opt/HA/written_status_en");
  }
}

function calendar($lang = "nl") {
  if ($lang === "nl") {
    return file_get_contents("/opt/HA/calendar_msg");
  } else {
       return shell_exec("trans -b nl:$lang \"".file_get_contents("/opt/HA/calendar_msg")."\" | tr -d '\\n'");
  }
}

function caller() {
    return file_get_contents("/opt/sascha/nextcloud/name.txt");
}

function busy_msg($lang = "nl") {
    if ($lang === "nl") {
       return file_get_contents("/opt/HA/busy_msg");
    } else {
       return shell_exec("trans -b nl:$lang \"".file_get_contents("/opt/HA/busy_msg")."\" | tr -d '\\n'");
    }
}

function greet() {
  global $lang;
$time = date("H");

if ($lang == "nl") {

if ($time < "12") {
    $greeting = "Goede morgen";
} elseif ($time < "18") {
    $greeting = "Goede middag";
} elseif ($time < "22") {
    $greeting = "Goede avond";
} else {
    $greeting = "Goede nacht";
}
} else {

if ($time < "12") {
    $greeting = "Good morning";
} elseif ($time < "18") {
    $greeting = "Good day";
} elseif ($time < "22") {
    $greeting = "Good evening";
} else {
    $greeting = "Good night";
}
}
  return $greeting;
}

?>
