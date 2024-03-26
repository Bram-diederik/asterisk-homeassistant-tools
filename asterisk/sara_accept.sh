#!/bin/bash

# Get the channel for 'process@voice_accept:'
channel_in=$(asterisk -x "core show channels" | grep 'process@voice_accept' | cut -d' ' -f1)

if [ -z "$channel_in" ]; then
channel_in=$(asterisk -x "core show channels" | grep 'wait@voice_accept' | cut -d' ' -f1)
#    asterisk -x "channel redirect $channel_in call,callallwithmessage,1"
    asterisk -x "channel redirect $channel_in call,callall,1"
else
    # Redirect the channel
#    asterisk -x "channel redirect $channel_in call,callallwithmessage,1"
    asterisk -x "channel redirect $channel_in call,callall,1"
fi
