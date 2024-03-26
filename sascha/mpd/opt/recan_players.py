#!/usr/bin/python3

import subprocess
import argparse
import yaml

def load_config(config_file):
    with open(config_file, 'r') as f:
        config = yaml.safe_load(f)
    return config

def rescan_database(player_name, host, port):
    subprocess.run(["mpc", "-p", str(port), "--host", host, "update"])

def main():
    parser = argparse.ArgumentParser(description='Rescan MPD database for multiple players.')
    parser.add_argument('--config', '-c', type=str, help='Path to the YAML configuration file')
    parser.add_argument('--player', '-p', type=str, help='Name of the MPD player')
    parser.add_argument('--host', '-H', type=str, help='IP address of the MPD server')
    parser.add_argument('--port', '-P', type=int, help='Port of the MPD server')
    args = parser.parse_args()

    if args.config:
        config = load_config(args.config)
        for player in config['players']:
            rescan_database(player['name'], player['host'], player['port'])
    elif args.player and args.host and args.port:
        rescan_database(args.player, args.host, args.port)
    else:
        parser.error('You must provide either a configuration file or player name, host, and port.')

if __name__ == "__main__":
    main()
