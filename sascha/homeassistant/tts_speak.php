#!/usr/bin/php
<?php

include("/opt/sascha/common.php");

if (count($argv) < 4) {
    die("Usage: php tts_speak.php <lang> <entity_id> \"<message>\"\n");
}

$sLang = $argv[1];
$entityId = $argv[2];
$sMessage = $argv[3];

$requestData = array(
    "entity_id" => $entityId,
    "message" => $sMessage,
    "language" => $sLang
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/services/tts/google_translate_say");
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

echo "TTS message sent to ".$entityId.".\n";
?>
