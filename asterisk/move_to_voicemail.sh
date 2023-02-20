#!/bin/bash
asterisk -x "channel redirect `asterisk -x "core show channels" | grep SIP/0100- | cut -d" " -f1` karin,1,1"
