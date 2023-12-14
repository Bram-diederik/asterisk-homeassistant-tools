#!/bin/bash
#rm /var/spool/asterisk/voicemail/default/6001/INBOX/*

# Source and destination directories
src_dir="/var/spool/asterisk/voicemail/default/6001/INBOX/"
dst_dir="/var/spool/asterisk/voicemail/default/6001/Old"

rm $src_dir/*
/opt/asterisk/parse_voicemail.php
