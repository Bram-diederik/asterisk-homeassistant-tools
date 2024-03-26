#!/usr/bin/php
<?php
include("/opt/sascha/sara/common.php");

$sSensorName = $argv[1]; // Get the sensor name from the command-line argument
$sValue = $argv[2]; // Get the value from the command-line argument

if (!($sSensorName && $sValue)) 
  die("set sensor and value");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/".$sSensorName);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

$stateObj = json_decode($result, true); // Decode the retrieved state object into an associative array
$stateObj["state"] = $sValue; // Modify the state value in the retrieved object

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/".$sSensorName);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Use HTTP PUT method
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stateObj)); // Send the modified state object
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

echo "$result \nSensor ".$sSensorName." set to ".$sValue.".\n";
?>
