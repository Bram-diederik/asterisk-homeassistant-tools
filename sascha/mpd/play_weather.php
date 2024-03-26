#!/usr/bin/php
<?php
$host = "192.168.5.2";
$port = "6660";
$xml = simplexml_load_file("https://feeds.buzzsprout.com/1257854.rss");

$url = $xml->channel->item[0]->enclosure['url'][0];
$title = $xml->channel->item[0]->title;

sleep(2);
system('mpc -q --host '.$host.' --port '.$port.' clear');
system('mpc -q --host '.$host.' --port '.$port.' add '.$url);
system('mpc -q --host '.$host.' --port '.$port.' play');

echo "Het weer";
?>
