#!/usr/bin/php
<?php
include("/opt/sascha/sara/common.php");

$sSensorName = $argv[1]; // Get the sensor name from the command-line argument
$sAttributeName = isset($argv[2]) ? $argv[2] : null; // Get the attribute name from the command-line argument (if provided)

if (!$sSensorName) {
  die("Sensor name is required.");
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/".$sSensorName);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

#print_r($result );

$stateObj = json_decode($result, true); // Decode the retrieved state object into an associative array

#print_r($stateObj);

if ($sAttributeName) {
  if (isset($stateObj['attributes'][$sAttributeName])) {
    echo $stateObj['attributes'][$sAttributeName];
  } else {
    die("Attribute not found.");
  }
} else if (@$stateObj["state"]) {
  echo $stateObj["state"];
} else {
die("no state found");
}
?>
