#!/usr/bin/php
<?php
if ($argc != 3) {
die("./start_call.php <from> <to>");
}
$from = $argv[1];
$to =  $argv[2];
$name = "Bram Diederik";
$context = "users";

system("/opt/asterisk/phone_hangup.sh $from");
  $call_file_contents = "Channel: Local/$from@$context\n";
  $call_file_contents .= "Context: $context\n";
  $call_file_contents .= "Extension: $to\n";
  $call_file_contents .= "Priority: 1\n";
  $call_file_contents .= "MaxRetries: 0\n";
  $call_file_contents .= "RetryTime: 60\n";
  $call_file_contents .= "WaitTime: 30\n";
  $call_file_contents .= "CallerID: $name\n";
  $call_file_name = "start_call$from$to";
  // Write the call file
  file_put_contents("/tmp/".$call_file_name, $call_file_contents);
  chmod("/tmp/".$call_file_name,0777);
  rename("/tmp/".$call_file_name,"/var/spool/asterisk/outgoing/".$call_file_name); 

?>
