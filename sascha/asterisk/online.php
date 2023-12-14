#!/usr/bin/php
<?php

$result = shell_exec('/opt/sascha/homeassistant/sensorget.php sensor.bool_asterisk_up_for_torch');
#print_r($result);
// Check the result and perform actions accordingly
if (trim($result) === 'off') {
    //phone system to torch down dont pickup
    echo "false";
    exit(1);

} else {
    echo "true";
    exit(0);
}
?>
