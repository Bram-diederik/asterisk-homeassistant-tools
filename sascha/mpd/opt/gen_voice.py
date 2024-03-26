#!/usr/bin/python3
import mysql.connector
import subprocess
import requests
import base64
import yaml


def search_artist(query):
    with open('players.yaml', 'r') as config_file:
        config = yaml.safe_load(config_file)
    client_id = config.get('client_id')
    client_secret = config.get('client_secret')
    access_token = get_access_token(client_id, client_secret)

    if access_token:
        search_url = 'https://api.spotify.com/v1/search'
        params = {
            'q': query,
            'type': 'artist'
        }
        headers = {
            'Authorization': 'Bearer ' + access_token
        }

        response = requests.get(search_url, params=params, headers=headers)
        data = response.json()

        if 'artists' in data and 'items' in data['artists'] and data['artists']['items']:
            return data['artists']['items'][0]

    return None


def get_access_token(client_id, client_secret):
    token_url = 'https://accounts.spotify.com/api/token'
    headers = {
        'Authorization': 'Basic ' + base64.b64encode((client_id + ':' + client_secret).encode()).decode()
    }
    payload = {
        'grant_type': 'client_credentials'
    }

    response = requests.post(token_url, headers=headers, data=payload)
    data = response.json()

    return data.get('access_token')

def playlist_spotify(cursor,playlist,artist):
    spotify_artist = search_artist(artist)
    query1 = "SELECT id FROM playlists WHERE playlist = %s"
    cursor.execute(query1, (playlist,))
    result = cursor.fetchone()
    if result:
        playlist_id = result[0]
        query3 = "SELECT count(*) FROM playlist_weblookup WHERE playlist_id = %s"
        cursor.execute(query3, (playlist_id,))
        count = cursor.fetchone()[0]
        if (count  == 0):
          query2 = "INSERT INTO playlist_weblookup  (playlist_id ,web_lookup) VALUES (%s,%s)"
          cursor.execute(query2, (playlist_id,spotify_artist['name'],))
          print(playlist_id,spotify_artist['name'])

def playlist_exists(cursor, playlist_name):
    query = "SELECT COUNT(*) FROM playlists WHERE name = %s"
    cursor.execute(query, (playlist_name,))
    count = cursor.fetchone()[0]
    return count > 0

def add_playlist_to_db(cursor, artist_name, playlist_name, artist_id):
    query = "INSERT INTO playlists (name, playlist, type) VALUES (%s, %s, %s)"
    cursor.execute(query, (artist_name, playlist_name, 2))

def main():
    with open('players.yaml', 'r') as config_file:
        config = yaml.safe_load(config_file)

    db_config = config['db'][0]  # Assuming the host configuration is the first element in the list

    cnx = mysql.connector.connect(
        host=db_config['host'],
        user=db_config['user'],
        password=db_config['password'],
        database=db_config['database']
    )


    cursor = cnx.cursor()

    access_token = get_access_token(config['client_id'], config['client_secret'])

    # Fetch playlists from MPD servers
    players = config.get('players')
    for player in players:
        mpd_host = player.get('host')
        mpd_port = player.get('port')
        script = player.get('script')
        name = player.get('name')
        if mpd_host and mpd_port:
            playlists_output = subprocess.check_output(['mpc', '-h', mpd_host, '-p', str(mpd_port), 'lsplaylist']).decode()
            playlists = playlists_output.split('\n')
            for playlist in playlists:
                if playlist:
                    playlist_name = playlist.strip()
                    artist_name = playlist_name.replace('mpddir_', '', 1)
                    if not playlist_exists(cursor, artist_name):
                        add_playlist_to_db(cursor, artist_name, playlist_name, None)
                    playlist_spotify(cursor,playlist_name,artist_name)

    cnx.commit()
    cursor.close()
    cnx.close()

if __name__ == "__main__":
    main()


