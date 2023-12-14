<?php

$sToken = "123456";
$caller = $_POST['caller'];


$caller = trim($caller);
// Check if the phone number begins with "31"
if (strpos($caller, '31') === 0) {
    // Replace "31" with "0"
    $caller = '0' . substr($caller, 2);
} else {
    // Add a "+" if the number doesn't start with "0"
    if (strpos($caller, '0') !== 0) {
        $caller = '+' . $caller;
    } 
}

$headers = getallheaders();

// Get the authorization token from the request headers
$authorizationHeader = $headers['Authorization'];

// Extract the token value from the header
$token = null;
if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
    $token = $matches[1];
}

// Verify the token and perform further actions
if ($token !== $sToken) {
   die("computer says no");
}

if (@$caller) {
     $caller = str_replace("+31", "0", $caller);
     // Execute the blacklist checking script and capture the output
     $out =  exec("sudo  /opt/asterisk/blacklist_last_number.sh  $caller",$var,$blacklistStatus);
      echo $out;
  }
  


?>
