#!/usr/bin/php
<?php
$name = file_get_contents("/opt/vcf2asterisk/name");
$url = "http://karin:8123";
$webhook = "-_K2dLJFDN3QLrBExrpZ8ON4E";
system("curl -s -X POST  -H \"Content-Type: application/json\" -d '{ \"name\":\"$name\" }' ".$url."/api/webhook/".$webhook);
?>
