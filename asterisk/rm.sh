#!/bin/bash
rm /var/spool/asterisk/voicemail/default/6001/INBOX/*
/opt/asterisk/parse_voicemail.php
