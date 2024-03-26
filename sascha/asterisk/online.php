#!/usr/bin/php
<?php

$result = shell_exec('/opt/sascha/homeassistant/sensorget.php sensor.bool_asterisk_up_for_glitch');
#print_r($result);
// Check the result and perform actions accordingly
if (trim($result) === 'off') {
    //phone system to glitch down dont pickup
    echo "false";
    exit(1);

} else {
    echo "true";
    exit(0);
}
?>
