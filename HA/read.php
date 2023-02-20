#!/usr/bin/php
<?php
//read status from home assistant cached values for optimal speed for asterisk
$dir = "/opt/HA/";
$webhook = "-npX04vXIOOI_Pjb-ESs9Cn5T";
$url = "http://karin:8123";

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
if  ($addressbook == "Very_close")
{
  echo 1;
  exit(1);
}
$sBeschikbaar = file_get_contents($dir."available");
$sBusy = file_get_contents($dir."busy");
$sTochBedroomCharing =  file_get_contents($dir."phone_change");
if ($sBeschikbaar == "on") {
   $bKarinPickup = false;
} else {
  #check busy
 if ($sBusy == "on") {
    $bKarinPickup = true;
 } else {
  $now =  new DateTime();
  $sleep_none = DateTime::createFromFormat('H:i', "21:00");
  $wakeup_none = DateTime::createFromFormat('H:i', "08:30");
  $sleep_all = DateTime::createFromFormat('H:i', "21:30");
  $wakeup_all = DateTime::createFromFormat('H:i', "08:30");
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
}

$name = file_get_contents("/opt/vcf2asterisk/name");
$name_number = file_get_contents("/opt/vcf2asterisk/name_number");

system("curl -s -X POST  -H \"Content-Type: application/json\" -d '{ \"key\": \"$name_number\",\"name\":\"$name\" }' ".$url."/api/webhook/".$webhook);


if ($bKarinPickup) {
  echo 0;
  exit(0);
} else {
  echo 1;
  exit(1);
}
?>
