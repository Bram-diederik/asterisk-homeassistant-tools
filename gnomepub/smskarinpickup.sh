#!/bin/bash
reciever=`cat /opt/vcf2asterisk/name_number`
echo $reciever
#echo $message
ssh daft@192.168.5.41 kdeconnect-cli -n torch --send-sms \"karin pickup $reciever\" --destination 0612345678  && echo true

if [ $? == 0 ]; then
  echo $?
fi
