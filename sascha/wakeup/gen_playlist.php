#!/usr/bin/php
<?php
include("/opt/sascha/common.php");

$http_asterisk_path = "/var/www/html/asterisk/";
$http_asterisk_url = "http://192.168.5.50/asterisk/";
$http_path = "/var/www/html/wakeup/";
$http_url = "http://192.168.5.50/wakeup/";


function vertaalDag($engelseDag) {
    $vertalingen = array(
        'Monday' => 'maandag',
        'Tuesday' => 'dinsdag',
        'Wednesday' => 'woensdag',
        'Thursday' => 'donderdag',
        'Friday' => 'vrijdag',
        'Saturday' => 'zaterdag',
        'Sunday' => 'zondag'
    );

    return $vertalingen[$engelseDag];
}

function vertaalMaand($engelseMaand) {
    $vertalingen = array(
        'January' => 'januari',
        'February' => 'februari',
        'March' => 'maart',
        'April' => 'april',
        'May' => 'mei',
        'June' => 'juni',
        'July' => 'juli',
        'August' => 'augustus',
        'September' => 'september',
        'October' => 'oktober',
        'November' => 'november',
        'December' => 'december'
    );

    return $vertalingen[$engelseMaand];
}




function begroeting() {
$datum = date("Y-m-d"); // Huidige datum ophalen in het formaat "YYYY-MM-DD"
$engelseDag = date("l", strtotime($datum)); // Dag van de week in Engels
$dag_vd_maand = date("j", strtotime($datum)); // Dag van de maand zonder voorloopnullen
$engelseMaand = date("F", strtotime($datum)); // Maand in Engels

$vertaaldeDag = vertaalDag($engelseDag);
$vertaaldeMaand = vertaalMaand($engelseMaand);


    $uur = date("H");

    if ($uur >= 6 && $uur < 12) {
        return "Goedemorgen het is $vertaaldeDag $dag_vd_maand $vertaaldeMaand";
    } elseif ($uur >= 12 && $uur < 18) {
        return "Goedemiddag het is $vertaaldeDag $dag_vd_maand $vertaaldeMaand";
    } else {
        return "Goedeavond het is $vertaaldeDag $dag_vd_maand  $vertaaldeMaand";
    }

}

$hello = begroeting();

  system("gtts-cli -l nl '".$hello."' >  ".$http_path."/hello.mp3");
  file_put_contents($http_path."/playlist.m3u", $http_url."/hello.mp3\n",  LOCK_EX);
  exec('/opt/asterisk/parse_voicemail.php');
  file_put_contents($http_path."/playlist.m3u",file_get_contents($http_asterisk_path."/playlist.m3u"), FILE_APPEND | LOCK_EX);
  
  $calendar = exec("/opt/sascha/homeassistant/sensorget.php sensor.calendar_item_first"); 
  if ($calendar  == "off") {
    $calendar  = "u heeft geen agenda punt";
  } else { 
   $calendar  = "eerste agenda punt: $calendar ";
}

 system("gtts-cli -l nl '".$calendar."' >  ".$http_path."/callendar.mp3");
  file_put_contents($http_path."/playlist.m3u", $http_url."/callendar.mp3\n",  FILE_APPEND | LOCK_EX);
  file_put_contents($http_path."/playlist.m3u", "http://192.168.5.2:8080/podcasts/538-nieuws.php\n",  FILE_APPEND | LOCK_EX);

?>
