#!/usr/bin/php
<?php
include('/opt/sascha/settings.php');

// Define the list of media_player entities and their corresponding Snapcast clients
$media_players = array(
    'spotify' => 'media_player.spotify',
    'mpd' => 'media_player.player',
);

// Define the Snapcast client that we want to switch media sources on
$snapcast_client = 'media_player.snapcast_client_XXXXXX';

// Make a request to Home Assistant to get the current state of the Snapcast client
$url = $sHomeApiUrl . '/api/states/' . $snapcast_client;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $sHomeApiKey));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Decode the response and get the current media source
$state = json_decode($response, true);
$current_source = $state['attributes']['source'];

// Determine the next media source to switch to
$sources = array_keys($media_players);
$current_index = array_search($current_source, $sources);
if ($current_index === false || $current_index >= count($sources) - 1) {
    $next_index = 0;
} else {
    $next_index = $current_index + 1;
}
$next_source = $sources[$next_index];
$next_player = $media_players[$next_source];
// Make a service call to Home Assistant to switch the media source on the Snapcast client
$url = $sHomeApiUrl . '/api/services/media_player/select_source';
$data = array(
    'entity_id' => $snapcast_client,
    'source' => $next_source,
);
$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\nAuthorization: Bearer $sHomeApiKey\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ),
);
$context  = stream_context_create($options);
$service_response = file_get_contents($url, false, $context);


// Make a request to Home Assistant to get the current state of the Snapcast client
$url = $sHomeApiUrl . '/api/states/' . $next_player;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $sHomeApiKey));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$state = json_decode($response, true);
$next_title = $state['attributes']['media_title'];


// Check if the service call was successful
if ($service_response === false) {
    echo "Fout: kan geen bron selecteren\n";
} else {
    echo "$next_source $next_title";
}
