#!/usr/bin/php
<?php

//This script is executed from asterisk and write the lookup to files
$dir = "/opt/vcf2asterisk/";

if ($argv[1] == "0100")
{
  file_put_contents($dir."name","anoniem");
  file_put_contents($dir."name_number",date("D d H:i ")."anoniem");
  file_put_contents($dir."group","none");
  die();
}
$db = new SQLite3('/opt/vcf2asterisk/phones.db');
$results = $db->query("SELECT * FROM contacts where number = '$argv[1]'");
$row = $results->fetchArray();
#print_r($row);

if ($row) {
  echo $row['name']."#!#".$row['group'];
  file_put_contents($dir."name",$row['name']);
  file_put_contents($dir."name_number",date("D d H:i ").$row['name'].' '.$argv[1]);
  file_put_contents($dir."group",$row['group']);
} else {
  file_put_contents($dir."name",$argv[1]);
  file_put_contents($dir."name_number",date("D d H:i ").$argv[1]);
  file_put_contents($dir."group","none");


}


?>

