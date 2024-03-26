#!/bin/bash

# Check if language argument is provided
if [ -z "$1" ]; then
    # Default to denylist,block_english,1
    language_arg="denylist,block_english,1"
else
    # Check if the provided language is valid
    if [ "$1" == "dutch" ]; then
        language_arg="denylist,block_dutch,1"
    elif [ "$1" == "english" ]; then
        language_arg="denylist,block_english,1"
    else
        echo "Invalid language argument. Please specify dutch or english."
        exit 1
    fi
fi

# Get the channel for 'process@voice_accept:'
channel_in=$(asterisk -x "core show channels" | grep 'process@voice_accept' | cut -d' ' -f1)

if [ -z "$channel_in" ]; then
channel_in=$(asterisk -x "core show channels" | grep 'wait@voice_accept' | cut -d' ' -f1)
    asterisk -x "channel redirect $channel_in $language_arg"
else
    # Redirect the channel using the language argument
    asterisk -x "channel redirect $channel_in $language_arg"
fi
