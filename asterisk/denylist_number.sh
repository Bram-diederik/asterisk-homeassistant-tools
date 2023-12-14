#!/bin/bash

# Check if a number is denied in Asterisk
# Usage: ./blacklist_last_number.sh <caller_number>

caller_number="$1"

# Use Asterisk CLI to query the blacklist database
blacklist_status=$(asterisk -rx "database get denylist $caller_number")

if [ "$blacklist_status" == "Value: 1" ]; then
    # Caller number is denied
    echo "Number $caller_number is denied."
    exit 1  
else
    # Caller number is not denied
    echo "Number $caller_number is not denied."
    exit 0  # Return zero exit code to indicate not denied
fi
