<?php

// Replace with your own Spotify API credentials
$mpd_host = "192.168.5.2";

$client_id = '7d8e3ac24228439fb5bb0a71e6079ac4';
$client_secret = '6fb905d5d83148b49aa28c2dcef9df5a';

$script = "script.spotify_playlist_huiskamer";


$aPlaylists = array('4dRH3BkApEgxDZyOAco3s0','68ZZktlA7YAsCnOov9nDx6','37i9dQZF1DX6J5NfMJS675','37i9dQZF1DXbITWG1ZJKYt','0XTgZJ28iOJLpwaTqeobfj');

// Replace with the artist name you want to search for
function playlist_image_url($playlist) {
global $client_id;
global $client_secret;

// Get the access token using client credentials flow
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Basic '.base64_encode($client_id.':'.$client_secret)
));
$response = curl_exec($ch);
curl_close($ch);

// Parse the JSON response and retrieve the access token
$data = json_decode($response, true);
$access_token = $data['access_token'];

// Build the API request URL to search for artists
$url = "https://api.spotify.com/v1/playlists/$playlist";

// Send the HTTP request to search for the artist
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer '.$access_token
));
$response = curl_exec($ch);
#print_r($response);
curl_close($ch);

// Parse the JSON response and retrieve the artist image URL
$data = json_decode($response, true);
$image_url = $data['images'][0]['url'];

$name = $data['name'];

// Output the image URL
return array($name,$image_url);
}


echo '
type: vertical-stack
cards:
  - type: horizontal-stack
    cards:
';

$i = 0;
foreach($aPlaylists as $sPlaylist) {
  if (trim($sPlaylist) != "") {
  $i++;
  if ($i == 4) {
    $i = 1;
    echo '
  - type: horizontal-stack
    cards:
';
  }
  $data = playlist_image_url($sPlaylist);
  echo '
      - type: custom:button-card
        entity_picture: '.$data[1].'
        tap_action:
          action: call-service
          service: '.$script.'
          service_data:
            playlist: '.$sPlaylist.'
            type: playlist
            name: '.$data[0].'
        show_entity_picture: true
        name: '.$data[0].'
        show_state: false
        size: 90px
        use_light_color: true
        style:
          top: 40%
          left: 13%
          width: 24%
';
   }
}



?>
