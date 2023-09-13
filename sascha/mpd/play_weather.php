#!/usr/bin/php
<?php
$host = "192.168.5.2";
$port = "6660";
$xml = simplexml_load_file("https://www.omnycontent.com/d/playlist/56ccbbb7-0ff7-4482-9d99-a88800f49f6c/e56062ec-8b1b-45a6-908e-ae2f00ec7025/d5c8db09-c7a0-4492-b94d-ae2f00ecad6d/podcast.rss");

$url = $xml->channel->item[0]->enclosure['url'][0];
$title = $xml->channel->item[0]->title;

#die($url);

system('/opt/sascha/mpd/morevolume.py '. str_replace("&", "\&",$url).' /var/www/html/weer/weer.mp3 15  >> /dev/null');
sleep(2);
system('mpc -q --host '.$host.' --port '.$port.' clear');
system('mpc -q --host '.$host.' --port '.$port.' add http://192.168.5.43/weer/weer.mp3');
system('mpc -q --host '.$host.' --port '.$port.' play');

echo "Het weer";
?>
