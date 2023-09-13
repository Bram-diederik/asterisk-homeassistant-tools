#!/usr/bin/php
<?php
include('/opt/sascha/settings.php');

// Define the list of media_player entities and their corresponding Snapcast clients
$media_players = array(
    'spotify' => 'media_player.spotify_bram_diederik',
    'mpd' => 'media_player.player',
);

// Define the Snapcast client that we want to switch media sources on
$snapcast_client = 'media_player.vision_snapcast_client';

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

// Make a service call to Home Assistant to switch the media source on the media playing at the Snapcast client
$url = $sHomeApiUrl . '/api/services/media_player/media_next_track';
$data = array(
    'entity_id' => $media_players[$current_source],
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

#print_r($service_response);
?>
