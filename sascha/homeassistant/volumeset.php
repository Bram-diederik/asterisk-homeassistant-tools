#!/usr/bin/php
<?php
include("/opt/sascha/common.php");

$sEntityId = $argv[1]; // Get the entity ID from the command-line argument
$iVolumeLevel = $argv[2]; // Get the desired volume level from the command-line argument

if (!($sEntityId && $iVolumeLevel)) 
  die("volumeset.php entity action");

$data = array(
    'entity_id' => $sEntityId,
    'volume_level' => $iVolumeLevel
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/services/media_player/volume_set");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer ".$sHomeApiKey,
  "Content-Type: application/json"
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Send the entity ID and volume level in the request body
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

echo "$result \nVolume of ".$sEntityId." set to ".$iVolumeLevel.".\n";
?>
