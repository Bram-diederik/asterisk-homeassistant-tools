#!/usr/bin/php
<?php
//This script connects to Home assistant gets sensor information and stores it in local files.
//You dont want to execute those delaying commands during an asterisk call

$asterisk_dir = "/opt/sascha/asterisk/";

require_once("/opt/sascha/common.php");


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

file_put_contents($asterisk_dir."available.txt",$sBeschikbaar);

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

file_put_contents($asterisk_dir."block_unknown.txt",$sBlock);


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
echo $asterisk_dir."calendar_item.txt";
file_put_contents($asterisk_dir."calendar_item.txt",$sCalendar);
file_put_contents($asterisk_dir."calendar_msg.txt",$cal_msg);

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
file_put_contents($asterisk_dir."busy.txt","on");
} else {
  file_put_contents($asterisk_dir."busy.txt","off");
}


#phone change in bedroom
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/sensor.glitch_laad_slaapkamer");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
curl_close($ch);
$sTochBedroomCharing = $result['state'];

if ($sTochBedroomCharing == "False") {
  file_put_contents($asterisk_dir."phone_change.txt","off");
} else {
  file_put_contents($asterisk_dir."phone_change.txt","on");
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
file_put_contents($asterisk_dir."busy_msg.txt",$result['state']);




#bram status
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/sensor.parsed_bram_status");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
curl_close($ch);
file_put_contents($asterisk_dir."written_status_nl.txt",$result['state']);


#bram status en
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/sensor.parsed_bram_status_en");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
curl_close($ch);
file_put_contents($asterisk_dir."written_status_en.txt",$result['state']);


#phone status
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/sensor.bool_asterisk_up_or_home");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
curl_close($ch);

file_put_contents($asterisk_dir."phone_available.txt",$result['state']);

#phone offline message
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/input_text.glitch_offline");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = yaml_parse(curl_exec($ch));
print_r($result);
curl_close($ch);

file_put_contents($asterisk_dir."glitch_offline_text.txt",$result['state']);


if (isset($result['last_changed'])) {
    $messageTime = strtotime($result['last_changed']);

    // Calculate the time passed since the message was set
    $timePassed = time() - $messageTime;

    // Convert seconds to a human-readable format (e.g., hours, minutes, seconds)
    $timePassedFormatted = gmdate("H:i:s", $timePassed);
    file_put_contents($asterisk_dir."glitch_offline_time.txt",$timePassedFormatted);

}


?>
