#!/usr/bin/php
<?php
$name = file_get_contents("/opt/vcf2asterisk/name");
$url = "http://homeassistant:8123";
$webhook = "webhookpassword";
system("curl -s -X POST  -H \"Content-Type: application/json\" -d '{ \"name\":\"$name\" }' ".$url."/api/webhook/".$webhook);
?>
