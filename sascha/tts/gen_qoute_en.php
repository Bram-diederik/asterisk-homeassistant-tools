#!/usr/bin/php
<?php
include("/opt/sascha/common.php");

$url = 'https://api.api-ninjas.com/v1/quotes';
$options = [
    'http' => [
        'header' => 'X-Api-Key: CIJSdtkhMPncU8d+tcATYA==iLETzVjE2gPtYbwP'
    ]
];
$context = stream_context_create($options);

$response = file_get_contents($url, false, $context);
$data = json_decode($response, true);
$quote  = false;

$quote = $data[0]['quote'];
$author = $data[0]['author'];
$category = $data[0]['category'];


$lang = "en";

$failbackmessage = "On the one hand, shopping is dependable: You can do it alone, if you lose your heart to something that is wrong for you, you can return it it's instant gratification and yet something you buy may well last for years. Author: Judith Krantz ";
$message = "$quote Author: $author";


$failbackfile = tts($failbackmessage,"nl");
if ($quote  && $file = tts($message,"nl")) {
#  file_put_contents("/opt/sascha/msg",$file);
  echo $file;
} else {
#  file_put_contents("/opt/sascha/msg",$failbackfile);
  echo $failbackfile;
}


?>
