#!/usr/bin/python3

import subprocess
import argparse
import yaml

def load_config(config_file):
    with open(config_file, 'r') as f:
        config = yaml.safe_load(f)
    return config

def process_playlists(player_name, host, port):
    playlists = subprocess.check_output(["mpc", "-p", str(port), "--host", host, "ls"]).decode("utf-8").split("\n")
    for playlist in playlists:
        if playlist.strip() != "":
            if playlist.startswith("playlist_"):
                pass
            elif playlist.startswith("mpddir_"):
                # Uncomment the line below if you want to remove directories
                # subprocess.run(["mpc", "-p", str(port), "--host", host, "rm", playlist])
                pass
            else:
                subprocess.run(["mpc", "-p", str(port), "--host", host, "clear"])
                subprocess.run(["mpc", "-p", str(port), "--host", host, "rm", playlist])
                subprocess.run(["mpc", "-p", str(port), "--host", host, "add", playlist])
                subprocess.run(["mpc", "-p", str(port), "--host", host, "shuffle"])
                subprocess.run(["mpc", "-p", str(port), "--host", host, "save", f"mpddir_{playlist}"])

def main():
    parser = argparse.ArgumentParser(description='Process MPD playlists for multiple players.')
    parser.add_argument('--config', '-c', type=str, help='Path to the YAML configuration file')
    parser.add_argument('--player', '-p', type=str, help='Name of the MPD player')
    parser.add_argument('--host', '-H', type=str, help='IP address of the MPD server')
    parser.add_argument('--port', '-P', type=int, help='Port of the MPD server')
    args = parser.parse_args()

    if args.config:
        config = load_config(args.config)
        for player in config['players']:
            process_playlists(player['name'], player['host'], player['port'])
    elif args.player and args.host and args.port:
        process_playlists(args.player, args.host, args.port)
    else:
        parser.error('You must provide either a configuration file or player name, host, and port.')

if __name__ == "__main__":
    main()
