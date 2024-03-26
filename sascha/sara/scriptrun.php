#!/usr/bin/php
<?php

include("/opt/sascha/sara/common.php");

$sScriptName = $argv[1]; 
$serviceData = isset($argv[2]) ? json_decode($argv[2], true) : array();

if (!$sScriptName) {
  die("Usage: php script.php <script_name> [service_data_json]\n");
}

$requestData = array(
    "entity_id" => "script.".$sScriptName
);

if (!empty($serviceData)) {
    $requestData["variables"] = $serviceData;
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/services/script/turn_on");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Logging the cURL request
echo "Request:\n";
echo json_encode($requestData, JSON_PRETTY_PRINT) . "\n";

$result = curl_exec($ch);

// Logging the cURL response
echo "Response:\n";
echo $result . "\n";

curl_close($ch);

echo "Script ".$sScriptName." triggered.\n";
?>
