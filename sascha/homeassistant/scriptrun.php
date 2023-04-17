#!/usr/bin/php
<?php

include("/opt/sascha/common.php");

$sScriptName = $argv[1]; 
if (!$sScriptName) 
  die("please add script name");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/services/script/turn_on");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    "entity_id" => "script.".$sScriptName
)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

echo "Script ".$sScriptName." triggered.\n";
?>
