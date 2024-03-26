#!/bin/bash
asterisk -x "channel request hangup `asterisk -x "core show channels" | grep PJSIP/$1- | cut -d" " -f1`"
