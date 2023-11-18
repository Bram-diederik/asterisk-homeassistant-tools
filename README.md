# asterisk-homeassistant-tools

# sascha 
asterisk number recognition, smart vociemail

This is an 21st century "voicemail" using asterisk ,nextcloud and home assistant.

Sascha picks up the call when your busy due to a schedule meeting (meeting members are allowed to call) or a manual switch or its late and gives the caller the option to
1 call voicemail.
2 continue to call. (behind password for not close persons)
3 give more info. (behind password for not close persons)



here is an (old) need to update youtube video https://www.youtube.com/watch?v=erOs4H4BXOA

![](./pics/asterisk.png)

# other code
this code also includes asterisk interface to control your home assistant with your sip phone.
select music and control switches.

there is also a script to create mpd playlist cards in home assistant

# atleast these (debian) packages are required
`apt install php php-yaml php-sqlite3 php-curl sox libsox-fmt-all xmlstarlet php-mysql`

# python pip 
`pip install gtts`


create a gsm tts message with gtts
```
gtts-cli 'Dear caller this is a message' | sox -t mp3 -  -r 8000 -c1 message.gsm
```

