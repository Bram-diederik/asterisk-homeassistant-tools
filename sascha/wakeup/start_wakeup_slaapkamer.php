#!/usr/bin/php
<?php
include("/opt/sascha/common.php");

  // Get the caller ID and wake-up time from the row
  echo "\nwakeup slaapkamer ";
  $call_file_contents = "Channel: Local/6100@users\n";
  $call_file_contents .= "CallerID: Wekservice <6000>\n";
  $call_file_contents .= "Context: users\n";
  $call_file_contents .= "Extension: 5005\n";
  $call_file_contents .= "Priority: 1\n";
  $call_file_contents .= "MaxRetries: 0\n";
  $call_file_contents .= "RetryTime: 60\n";
  $call_file_contents .= "WaitTime: 30\n";
  $call_file_name = "wakeup_slaapkamer.call";

  echo $call_file_name .$call_file_contents;
  // Write the call file
  file_put_contents("/tmp/".$call_file_name, $call_file_contents);
  chmod("/tmp/".$call_file_name,0777);
  rename("/tmp/".$call_file_name,"/var/spool/asterisk/outgoing/".$call_file_name); 


?>
