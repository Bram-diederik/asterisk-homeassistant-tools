#!/bin/bash
context="busy" # a destination context
exten=12c # an operator's number
filter=all!callall!2
num="pickup" # a destination number
channel=$(asterisk -rx 'core show channels concise'  | grep PJSIP/$exten- | grep "$filter" | cut -d '!' -f1) # gets a connected channel, where its name is $
#echo $channel

result=$(asterisk -rx "channel redirect $channel $context,$num,1") # transfers an extracted channel into the destination context
echo $result
exit 0
