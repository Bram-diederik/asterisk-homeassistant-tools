#!/usr/bin/php
<?php
include("/opt/sascha/common.php");

function ha_create_phone_sensor($person, $phone_number) {
    global $sHomeApiUrl;
    global $sHomeApiKey;
    global $debug;
    
    if (!$sHomeApiUrl) {
        return false;
    }

    // Prepare the data for the sensor entity
    $entityId = "sensor.tel_" . str_replace(" ", "_", $person);
    $attributes = [
        "type" => "weblink",
        "friendly_name" => "Tel $person",
        "icon" => "mdi:phone",
        "unit_of_measurement" => "",
        "value" => "tel:$phone_number",
        "url" => "tel:$phone_number",
        "content" => "tel:$phone_number"
    ];
    
    // Prepare the payload
    $data = [
        "state" => "tel:$phone_number",
        "attributes" => $attributes
    ];
    
    if ($debug) 
        print_r($data);

    // Convert the payload to JSON
    $jsonPayload = json_encode($data);

    // Set the headers
    $headers = [
        "Authorization: Bearer $sHomeApiKey",
        "Content-Type: application/json"
    ];

    // Send the POST request to create/update the sensor entity
    $ch = curl_init($sHomeApiUrl . "/api/states/$entityId");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    // Check the response status
    if ($response === false) {
        echo "Failed to create/update the phone number sensor entity." . PHP_EOL;
    } else {
        echo "Phone number sensor entity created/updated successfully." . PHP_EOL;
    }
}

// Example usage:
$person = "John Doe";
$phone_number = "+1234567890";

ha_create_phone_sensor($person, $phone_number);

?>

