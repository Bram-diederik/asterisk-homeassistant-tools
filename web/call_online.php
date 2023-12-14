<?php

$sToken = "123456";

//$headers = getallheaders();

// Get the authorization token from the request headers
$authorizationHeader = $headers['Authorization'];

// Extract the token value from the header
$token = null;
if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
    $token = $matches[1];
}

// Verify the token and perform further actions
if ($token !== $sToken) {
 //  die("computer says no");
}
exec("sudo  /opt/sascha/asterisk/online.php",$var,$status);

if ($status === 0) {
     echo "true";
} else {
     echo "false";
}

?>
