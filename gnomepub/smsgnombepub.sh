#!/bin/bash
for ((i = 2; i <= $#; ++i)); 
  do message="$message ${!i}"; done

#echo $message
ssh daft@192.168.5.41 kdeconnect-cli -n torch --send-sms \"$message\" --destination $1

