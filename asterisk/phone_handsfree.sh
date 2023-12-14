#!/bin/bash

# Get the channel information
channels=$(asterisk -x "core show channels" | grep PJSIP/12c- | cut -d" " -f1)

# Check if any channels are found
if [ -n "$channels" ]; then
    # Execute the redirect command
    asterisk -x "channel redirect $channels karin,6,1"
else
    echo "No matching channels found."
fi
