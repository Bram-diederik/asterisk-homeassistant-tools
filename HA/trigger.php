#!/usr/bin/php
<?php
//This script connects to Home assistant gets sensor information and stores it in local files.
//You dont want to execute those delaying commands during an asterisk call

$dir = "/opt/HA/";
$sHomeApiUrl = "https://home.pi-cloud.gotdns.ch";
$sHomeApiKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJiZDQ0YWZkYTE5ZTY0Yzg0OTZjOGM1MGUwYWRjZDE4NyIsImlhdCI6MTY2OTkwNzUzOSwiZXhwIjoxOTg1MjY3NTM5fQ.MwdQz9m0qmCpJ5jXsxoYkLC-NPnBhB_t0Mc93ptVFQE";




#check avilable
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/input_boolean.beschikbaar");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
#curl_exec($ch);
curl_close($ch);
#print_r($result);
$sBeschikbaar = $result['state'];

file_put_contents($dir."available",$sBeschikbaar);

#check  block unknown
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/input_boolean.block_unknown_numbers");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
#curl_exec($ch);
curl_close($ch);
#print_r($result);
$sBlock = $result['state'];

file_put_contents($dir."block_unknown",$sBlock);


#check  callendar
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/calendar.persoonlijk");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
#print_r($result);
if ($result['state']) {
   $cal_msg = $result['attributes']['message'];
   $timeStart = strtotime($result['attributes']['start_time']);
   $timeStart = $timeStart - (15 * 60);
   $timeEnd = strtotime($result['attributes']['end_time']);
  #echo "$timeStart $timeEnd" + time();
   if (time() > $timeStart && time() < $timeEnd) {
    $sCalendar = "on";
  } else {
    $sCalendar = "off";
  }

} else {
  $sCalendar = "off";
}
file_put_contents($dir."calendar_item",$sCalendar);
file_put_contents($dir."calendar_msg",$cal_msg);

curl_close($ch);

#check busy
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/input_boolean.busy");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
#curl_exec($ch);
curl_close($ch);
$sBusy = $result['state'];

if ( $sBusy == "on") {
file_put_contents($dir."busy","on");
} else {
  file_put_contents($dir."busy","off");
}


#phone change in bedroom
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/sensor.torch_laad_slaapkamer");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
curl_close($ch);
$sTochBedroomCharing = $result['state'];

if ($sTochBedroomCharing == "False") {
  file_put_contents($dir."phone_change","off");
} else {
  file_put_contents($dir."phone_change","on");
}


#bram bezig_beschrijving
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/input_text.bezig_beschrijving");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
curl_close($ch);
file_put_contents($dir."busy_msg",$result['state']);




#bram status
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/sensor.parsed_bram_status");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
curl_close($ch);
file_put_contents($dir."written_status_nl",$result['state']);


#bram status en
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/sensor.parsed_bram_status_en");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
curl_close($ch);
file_put_contents($dir."written_status_en",$result['state']);






?>
