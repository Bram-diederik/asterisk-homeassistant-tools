#!/usr/bin/php
<?php
include("/opt/sascha/common.php");



$conn = mysqli_connect($dbservername,$dbusername, $dbpassword, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM wakeups WHERE wakeup_time <= ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
  die("Error: " . mysqli_error($conn));
}
// Bind the parameter to the SQL query
$now = date('Y-m-d H:i:s');
mysqli_stmt_bind_param($stmt, "s", $now);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

// Loop through the results and output them
while ($row = mysqli_fetch_assoc($result)) {
  print_r($row);
  $caller_id = $row['caller_id'];
  $wakeup_time = $row['wakeup_time'];


$stmt2 = $conn->prepare('DELETE FROM wakeups WHERE caller_id = ?');
$stmt2->bind_param('s', $caller_id);
  $stmt2->execute();

$bNewsDownload = true;
// Loop through the results
  if ($bNewsDownload ) {
     //Update news
     system("/opt/sascha/sounds/nunl.php");
     $bNewsDownload = false;
  } 
  // Get the caller ID and wake-up time from the row
  echo "\nwakeup $caller_id $wakeup_time ";
  $call_file_contents = "Channel: Local/$caller_id@users\n";
  $call_file_contents .= "Context: users\n";
  $call_file_contents .= "Extension: 5004\n";
  $call_file_contents .= "Priority: 1\n";
  $call_file_contents .= "MaxRetries: 0\n";
  $call_file_contents .= "RetryTime: 60\n";
  $call_file_contents .= "WaitTime: 30\n";
  $call_file_contents .= "CallerID: Wekservice <6000>\n";
  $call_file_name = "wakeup_$caller_id.call";
echo $call_file_name .$call_file_contents;
  // Write the call file
  file_put_contents("/tmp/".$call_file_name, $call_file_contents);
  chmod("/tmp/".$call_file_name,0777);
  rename("/tmp/".$call_file_name,"/var/spool/asterisk/outgoing/".$call_file_name); 

  // Delete the wake-up time from the database
  
  sleep(3);
}

?>
