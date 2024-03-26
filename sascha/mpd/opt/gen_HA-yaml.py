#!/usr/bin/python3
import argparse
import subprocess
import os
import yaml
import requests
import base64


def copy_img(url, artist_name, img_dir):
    img_path = "/local/images/mpd/"
    file_name = artist_name + '.jpg'
    destination = os.path.join(img_dir, file_name)

    if not os.path.exists(destination):
        if subprocess.run(["wget", "-O", destination, url]).returncode == 0:
            return img_path + file_name
        else:
            print("##Failed to copy file##")
            return None
    else:
        return img_path + file_name


def artist_image_url(artist_name, client_id, client_secret, img_dir):
    # Get the access token using client credentials flow
    token_url = 'https://accounts.spotify.com/api/token'
    headers = {
        'Authorization': 'Basic ' + base64.b64encode((client_id + ':' + client_secret).encode()).decode()
    }
    token_response = requests.post(token_url, data={'grant_type': 'client_credentials'}, headers=headers)
    token_data = token_response.json()
    access_token = token_data.get('access_token')

    # Search for the artist
    search_url = f'https://api.spotify.com/v1/search?q={artist_name}&type=artist&limit=1'
    headers = {'Authorization': 'Bearer ' + access_token}
    search_response = requests.get(search_url, headers=headers)
    search_data = search_response.json()
    artist_id = search_data.get('artists', {}).get('items', [])[0].get('id')

    # Get the artist information
    artist_url = f'https://api.spotify.com/v1/artists/{artist_id}'
    artist_response = requests.get(artist_url, headers=headers)
    artist_data = artist_response.json()
    if artist_data.get('images'):
        image_url = artist_data['images'][0]['url']
        file_name = f"{artist_name}.jpg"
        destination = os.path.join(img_dir, file_name)

        # Download the image if it doesn't exist
        if not os.path.exists(destination):
            with open(destination, 'wb') as f:
                image_response = requests.get(image_url)
                f.write(image_response.content)

        return file_name  # Assuming the file is saved with the artist's name
    else:
        return None  # Return None if no image is available


def main():
    parser = argparse.ArgumentParser(description='Generate Home Assistant YAML for MPD playlists.')
    parser.add_argument('--config', '-c', type=str, help='Path to the YAML configuration file')
    args = parser.parse_args()

    if args.config:
        with open(args.config, 'r') as f:
            config = yaml.safe_load(f)
            print("Config:", config)

        players = config.get('players')
        client_id = config.get('client_id')
        client_secret = config.get('client_secret')
        ha_www_path = config.get('ha_www_path')
        if players and client_id and client_secret:
            for player in players:
                mpd_host = player.get('host')
                mpd_port = player.get('port')
                script = player.get('script')
                name = player.get('name')

                print("MPD Host:", mpd_host)
                print("MPD Port:", mpd_port)
                print("Script:", script)

                if not all([mpd_host, mpd_port, script]):
                    print("Error: Missing required configuration values.")
                    return

                sPlaylists = subprocess.check_output(["mpc", "--host", mpd_host, "-p", str(mpd_port), "lsplaylists"]).decode("utf-8")
                aPlaylists = list(set(sPlaylists.splitlines()))
                aPlaylists.sort()
                sPrefix = "mpddir_"

                i = 0
                output_dir = "yaml_conf"
                img_dir = "images"  # Directory to save album art images
                os.makedirs(output_dir, exist_ok=True)
                os.makedirs(img_dir, exist_ok=True)

                output_file_path = os.path.join(output_dir, f"generated_yaml_{name}.yaml")
                with open(output_file_path, "w") as output_file:
                    output_file.write("type: vertical-stack\n")
                    output_file.write("cards:\n")
                    output_file.write("  - type: horizontal-stack\n")
                    output_file.write("    cards:\n")

                    for sPlaylist in aPlaylists:
                        sPlaylist = sPlaylist.strip()
                        if sPlaylist:
                            if sPlaylist.startswith(sPrefix):
                                i += 1
                                sPlaylistName = sPlaylist.replace(sPrefix, '')
                                if i == 4:
                                    i = 1
                                    output_file.write('  - type: horizontal-stack\n    cards:\n')
                                output_file.write(f'''
      - type: custom:button-card
        entity_picture: {ha_www_path}{artist_image_url(sPlaylistName, client_id, client_secret, img_dir)}
        tap_action:
          action: call-service
          service: {script}
          service_data:
            playlist: {sPlaylist}
            type: playlist
        show_entity_picture: true
        name: {sPlaylistName}
        show_state: false
        size: 90px
        use_light_color: true
        style:
          top: 40%
          left: 13%
          width: 24%
''')
                print(f"Generated YAML file for {name}: {output_file_path}")

    else:
        print("Error: No configuration file provided.")


if __name__ == "__main__":
    main()
