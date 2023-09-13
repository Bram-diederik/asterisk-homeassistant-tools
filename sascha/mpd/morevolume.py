#!/usr//bin/python3
import sys
import os
import requests
from pydub import AudioSegment

def download_file(url, save_path):
    try:
        response = requests.get(url)
        response.raise_for_status()

        with open(save_path, "wb") as file:
            file.write(response.content)

        print(f"Downloaded {url} to {save_path}")
    except requests.exceptions.RequestException as e:
        print(f"Error downloading {url}: {e}")

def increase_volume(input_path, output_path, gain_db):
    try:
        # Load the input audio file
        audio = AudioSegment.from_file(input_path)

        # Increase the volume
        louder_audio = audio + gain_db

        # Save the modified audio
        louder_audio.export(output_path, format="mp3")  # Change the format if needed
        
        print(f"Modified audio saved to {output_path}")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Usage: <input_audio_url> <output_audio> <volume_increase>")
    else:
        input_url = sys.argv[1]
        output_file = sys.argv[2]
        volume_increase_db = int(sys.argv[3])
        
        # Download the input audio file
        input_file = "temp_audio.mp3"
        download_file(input_url, input_file)

        # Increase volume and save modified audio
        increase_volume(input_file, output_file, volume_increase_db)

        # Clean up temporary downloaded file
        if os.path.exists(input_file):
            os.remove(input_file)
