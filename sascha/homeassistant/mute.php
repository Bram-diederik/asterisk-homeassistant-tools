#!/usr/bin/php
<?php
include("/opt/sascha/common.php");

$sEntityId = $argv[1]; // Get the entity ID from the command-line argument
$action =  $argv[2];

if (!($sEntityId && $action)) 
  die("mute.php entity action");

$data = array(
    'entity_id' => $sEntityId
);

if ($action == "on") {

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/services/media_player/volume_mute");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
  ));
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array_merge($data, array('is_volume_muted' => true)))); // Send the entity ID and mute flag in the request body
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  echo "$result \nMedia player ".$sEntityId." muted.\n";
} else {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/services/media_player/volume_mute");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
  ));
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array_merge($data, array('is_volume_muted' => false)))); // Send the entity ID and unmute flag in the request body
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  echo "$result \nMedia player ".$sEntityId." unmuted.\n";
}
?>

