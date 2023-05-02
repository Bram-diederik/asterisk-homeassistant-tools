#!/bin/bash

mpc --host 192.168.5.40 del $(mpc --host 192.168.5.40 current -f %position%)


playlist=`mpc --host 192.168.5.40 playlist -f "%file%" | head -n 1|  sed 's|/.*||' | sed 's/ /\_/g'`
mpc --host 192.168.5.40 rm $playlist
mpc --host 192.168.5.40 save $playlist


