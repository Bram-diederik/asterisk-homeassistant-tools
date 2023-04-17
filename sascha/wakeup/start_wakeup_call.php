#!/usr/bin/php
<?php
include("/opt/sascha/common.php");
$conn = mysqli_connect($sqlHost,$sqlUser, $sqlPasswd, $sqlDb);

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
#die();
  // Check if the caller ID starts with "6"
  if (substr($caller_id, 0, 1) === "6") {
    // Create the call file with Local channel
    $call_file_contents = "Channel: Local/$caller_id@users\n";
    $call_file_contents .= "Context: users\n";
    $call_file_contents .= "Extension: 4013\n";
    $call_file_contents .= "Priority: 1\n";
    $call_file_contents .= "MaxRetries: 0\n";
    $call_file_contents .= "RetryTime: 60\n";
    $call_file_contents .= "WaitTime: 30\n";
    $call_file_name = "wakeup_$caller_id.call";
  } else {
    // Create the call file with SIP channel and wait time
    $call_file_contents = "Channel: LOCAL/wakeup_out@wakeup\n";
    $call_file_contents .= "Context: wakeup";
    $call_file_contents .= "Priority: 1\n";
    $call_file_name = "wakeup_sip_$caller_id.call";
    system("asterisk -rx 'database put phones sacha/wakeupcall $caller_id'");
  }
echo $call_file_name .$call_file_contents;
  // Write the call file
  file_put_contents("/tmp/".$call_file_name, $call_file_contents);
  chmod("/tmp/".$call_file_name,0777);
  rename("/tmp/".$call_file_name,"/var/spool/asterisk/outgoing/".$call_file_name); 

  // Delete the wake-up time from the database
  
  sleep(3);
}

?>
