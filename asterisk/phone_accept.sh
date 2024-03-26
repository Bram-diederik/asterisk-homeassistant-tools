#!/bin/bash
asterisk -x "channel redirect `asterisk -x "core show channels" | grep PJSIP/t- | cut -d" " -f1` karin,2,1"
