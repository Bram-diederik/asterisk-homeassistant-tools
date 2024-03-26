#!/bin/bash

# Get the channel for 'process@voice_accept:'
channel_in=$(asterisk -x "core show channels" | grep 'process@voice_accept' | cut -d' ' -f1)

if [ -z "$channel_in" ]; then
#        echo "No channels found for process@voice_accept"
        channel_in=$(asterisk -x "core show channels" | grep 'wait@voice_accept' | cut -d' ' -f1)
    asterisk -x "channel redirect $channel_in voice_accept,noaccess,1"
else
    # Redirect the channel
    asterisk -x "channel redirect $channel_in voice_accept,noaccess,1"
fi
