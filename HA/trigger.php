#!/usr/bin/php
<?php
//This script connects to Home assistant gets sensor information and stores it in local files.
//You dont want to execute those delaying commands during an asterisk call

$dir = "/opt/HA/";
$sHomeApiUrl = "https://home.mouse.h-o-s-t.name";
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


#check  callendar
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/calendar.persoonlijk");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));

if ($result['state'] == "on") {
   $timeStart = strtotime($result['attributes']['start_time']);
   $timeStart = $timeStart - (10 * 60);
   $timeEnd = strtotime($result['attributes']['end_time']);
   if (time() > $timeStart && time() < $timeEnd) {
    $bCalendar = true;
  } else {
    $bCalendar = false;
  }

} else {
  $bCalendar = false;
}
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

if ($bCalendar || $sBusy == "on") {
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

?>
