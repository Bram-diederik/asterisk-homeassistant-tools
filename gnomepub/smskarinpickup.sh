#!/bin/bash
reciever=`cat /opt/vcf2asterisk/name_number`
echo $reciever
#echo $message
ssh daft_dutch@gnomepub kdeconnect-cli -n torch --send-sms \"karin pickup $reciever\" --destination 0612345678

