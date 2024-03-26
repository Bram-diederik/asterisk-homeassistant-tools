#!/usr/bin/php
<?php

include("/opt/sascha/common.php");

$sEntityId = $argv[1];
$temperature = $argv[2];

if (!$sEntityId || !$temperature) {
    die("Usage: php thermostateset.php <climate entity> <temperature>\n");
}

$requestData = array(
        "entity_id" => $sEntityId,
        "temperature" => $temperature
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl . "/api/services/climate/set_temperature");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $sHomeApiKey,
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

echo "Climate set_temperature script triggered for entity $sEntityId.\n";
?>
