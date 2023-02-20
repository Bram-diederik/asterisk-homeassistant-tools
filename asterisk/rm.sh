#!/bin/bash
rm /var/spool/asterisk/voicemail/default/1000/INBOX/*
/opt/asterisk/parse_voicemail.php
