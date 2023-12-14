#!/bin/bash
asterisk -x "channel redirect `asterisk -x "core show channels" | grep SIP/12c- | cut -d" " -f1` karin,7,1"
