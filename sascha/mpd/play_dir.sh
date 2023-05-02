#!/bin/bash
# Set the directory name and playlist name from command line arguments
dir_name="$1" 
#dir_name2=`echo $dir_name | sed 's/ /\_/g'`
host="192.168.5.40"
# Try to load the playlist
mpc --host $host clear 
mpc --host $host load "$dir_name" 
# Check if the playlist was successfully loaded
if [ $? -eq 0 ]; then 
  echo "Playlist exists. Shuffling and playing..."
mpc --host $host shuffle 
mpc --host $host play
  # Shuffle the playlist
  # mpc --host $host shuffle && mpc --host $host play 
else 
  echo "Playlist does not exist. Loading folder..."
  # Load the folder with the same name as the playlist
  mpc --host $host clear 
  mpc --host $host add "$dir_name" 
 if [ $? -eq 0 ]; then   
  mpc --host $host shuffle 
  mpc --host $host play 
  mpc --host $host save "$dir_name"
 fi
fi
