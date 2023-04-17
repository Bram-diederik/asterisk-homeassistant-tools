#!/usr/bin/php
<?php
include("/opt/sascha/common.php");

$sEntityId = $argv[1]; // Get the entity ID from the command-line argument
$action =  $argv[2];

if (!($sEntityId && $action)) 
  die("switch.php entity action");

$data = array(
    'entity_id' => $sEntityId
);

if ($action == "on") {

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/services/switch/turn_on");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
  ));
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Send the entity ID in the request body
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  echo "$result \nSwitch ".$sEntityId." turned on.\n";
} else {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/services/switch/turn_off");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
  ));
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Send the entity ID in the request body
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  echo "$result \nSwitch ".$sEntityId." turned off.\n";
}
?>
