#!/bin/bash

mpc --host 192.168.5.2 del $(mpc --host 192.168.5.2 current -f %position%)


playlist=`mpc --host 192.168.5.2 playlist -f "%file%" | head -n 1|  sed 's|/.*||' | sed 's/ /\_/g'`
mpc --host 192.168.5.2 rm $playlist
mpc --host 192.168.5.2 save $playlist


