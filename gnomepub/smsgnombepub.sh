#!/bin/bash
for ((i = 2; i <= $#; ++i)); 
  do message="$message ${!i}"; done

#echo $message
ssh daft_dutch@gnomepub kdeconnect-cli -n torch --send-sms \"$message\" --destination $1

