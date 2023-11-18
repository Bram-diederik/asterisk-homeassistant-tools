#!/usr/bin/php
<?php
//read status from home assistant cached values for optimal speed for asterisk
$asterisk_dir = "/opt/sascha/asterisk/";

require_once("/opt/sascha/common.php");

//HARDCODED VARIBLES
//the addressbook switches are hard coded
//Very_close is set to always calls. 
//close = close friends and family
//all the global address list
//none unknown number

//Lots of variables are bind to these 4(3) settings


//torch is my phone. and it will be checked if its charching in the bedroom


if (@$argv[1]) {
  $addressbook = $argv[1];
}
else {
  $addressbook = "none";
}
if (@$argv[2]) {
  $stop_at_veryClose = $argv[2];
}
else {
   $stop_at_veryClose = 1;
}
if  ($addressbook == "very close")
{
  if ($stop_at_veryClose) {
    echo 1;
    $name_number = file_get_contents("/opt/sascha/nextcloud/name_number.txt"); 
    system("/opt/sascha/homeassistant/scriptrun.php phonebook_update '{ \"name\": \"$name_number\" }'");
    exit(1);
  } else {
    $addressbook = "close";
  }
}


$sBeschikbaar = file_get_contents($asterisk_dir."available.txt");
$sBusy = file_get_contents($asterisk_dir."busy.txt");
$sCalendar = file_get_contents($asterisk_dir."calendar_item.txt");
$sBlock = file_get_contents($asterisk_dir."block_unknown.txt");
$sTochBedroomCharing =  file_get_contents($asterisk_dir."phone_change.txt");
if ($sBlock == "on" && $addressbook == "none") {
    $bKarinPickup = true;
} elseif ($sBeschikbaar == "on") {
   $bKarinPickup = false;
} 
else if ($sCalendar == "on") {
    $bKarinPickup = true;
 } else if ($sBusy == "on") {
    $bKarinPickup = true;
 } else {

  $now =  new DateTime();
  $sleep_none = DateTime::createFromFormat('H:i', "21:00");
  $wakeup_none = DateTime::createFromFormat('H:i', "08:00");
  $sleep_all = DateTime::createFromFormat('H:i', "21:30");
  $wakeup_all = DateTime::createFromFormat('H:i', "08:00");
  $sleep_close = DateTime::createFromFormat('H:i', "23:30");
  $wakeup_close = DateTime::createFromFormat('H:i', "07:30");
  $sleep_torch_close = DateTime::createFromFormat('H:i', "21:30");
  $wakeup_torch_close = DateTime::createFromFormat('H:i', "07:30");
 

   if ($addressbook  == "none" && ($sleep_none < $now || $wakeup_none > $now)) {
     $bKarinPickup = true;
   } else if ($addressbook  == "all" && ($sleep_all < $now || $wakeup_all > $now)) {

    $bKarinPickup = true;
   } else if ($addressbook  == "close" ) {
     if ($sTochBedroomCharing == "on" &&  ($sleep_torch_close < $now || $wakeup_torch_close >$now)) 
     {
       $bKarinPickup = true;
     } else if ($sleep_close < $now || $wakeup_close > $now )
     {
       $bKarinPickup = true;
     }
  }
}

$name_number = file_get_contents("/opt/sascha/nextcloud/name_number.txt"); 
system("/opt/sascha/homeassistant/scriptrun.php phonebook_update '{ \"name\": \"$name_number\" }' > /dev/null 2>&1");


if ($bKarinPickup) {
  echo 0;
  exit(0);
} else {
  echo 1;
  exit(1);
}
?>
