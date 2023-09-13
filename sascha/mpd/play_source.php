#!/usr/bin/php
<?php

$host = "192.168.5.2";

system('mpc --host '.$host.' clear');
system('mpc --host '.$host.' add '.$argv[1]);
system('mpc --host '.$host.' play');
?>
