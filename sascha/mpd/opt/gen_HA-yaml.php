<?php

// Replace with your own Spotify API credentials
$mpd_host = "192.168.5.2";

$client_id = 'spotify_id';
$client_secret = 'spotify_password';

$script = "script.mpd_playlist_slaapkamer";

$img_dir = "./images/";
$img_path = "/local/images/";

function copy_img($url) {
  global $img_dir;
  global $img_path;
  $file_name = basename($url);

  $destination = $img_dir . $file_name;

  if (copy($url, $destination)) {
  } else {
    echo "##Failed to copy file##\n";
  }
  return $img_path . $file_name;
}



// Replace with the artist name you want to search for
function artist_image_url($artist_name) {
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
$url = "https://api.spotify.com/v1/search?q=".urlencode($artist_name)."&type=artist&limit=1";

// Send the HTTP request to search for the artist
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer '.$access_token
));
$response = curl_exec($ch);
curl_close($ch);

// Parse the JSON response and retrieve the artist ID
$data = json_decode($response, true);
$artist_id = $data['artists']['items'][0]['id'];

// Build the API request URL to get the artist information
$url = "https://api.spotify.com/v1/artists/".$artist_id;

// Send the HTTP request to retrieve the artist information
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer '.$access_token
));
$response = curl_exec($ch);
curl_close($ch);

// Parse the JSON response and retrieve the artist image URL
$data = json_decode($response, true);
$image_url = copy_img($data['images'][0]['url']);

// Output the image URL
return $image_url;
}


echo '
type: vertical-stack
cards:
  - type: horizontal-stack
    cards:
';

$sPlaylists = shell_exec("mpc --host $mpd_host lsplaylists");

$aPlaylists = explode("\n",$sPlaylists);
$aPlaylists = array_unique($aPlaylists );
asort($aPlaylists);

$i = 0;
foreach($aPlaylists as $sPlaylist) {
  if (trim($sPlaylist) != "") {
  $sPrefix = "mpddir_";

  if (strpos($sPlaylist, $sPrefix) === 0) {
  $i++;
    $sPlaylistName = substr($sPlaylist, strlen($sPrefix));
    if ($i == 4) {
      $i = 1;
      echo '
  - type: horizontal-stack
    cards:
';
  } 
    echo '
      - type: custom:button-card
        entity_picture: '.artist_image_url($sPlaylistName).'
        tap_action:
          action: call-service
          service: '.$script.'
          service_data:
            playlist: '.$sPlaylist.'
            type: playlist
        show_entity_picture: true
        name: '.$sPlaylistName.'
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
}

?>
