#!/bin/bash
asterisk -x "channel redirect `asterisk -x "core show channels" | grep process@voice_accept | cut -d" " -f1` voice_accept,wait,1"
