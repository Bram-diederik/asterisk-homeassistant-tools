<?php

$sToken = "123456";
$caller = $_POST['caller'];

$caller = trim($caller);
// Add a "+" if the number doesn't start with "0"

if ($caller == "anonymous" ) {

} elseif (strpos($caller, '0') !== 0) {
    $caller = '+' . $caller;
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
       exec("sudo  /opt/sascha/asterisk/read_number.php $caller",$var,$status);

       if ($status === 1) {
            echo "true";
        } else {
            echo "false";
        }
  }
  


?>
