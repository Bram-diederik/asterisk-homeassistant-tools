# asterisk-homeassistant-tools
asterisk number recognition, HA-SIP use cases

This is an 22st solution for an awnsering machine using asterisk and home assistant.
using the sip-ha addon https://github.com/arnonym/ha-plugins

sip-ha picks up the phone when busy and explains the status.
and provides 3 options
1 call voicemail.
2 continue to call. (behind password for not close persons)
3 give more info. (behind password for not close persons)


here is an (old) need to update youtube video https://www.youtube.com/watch?v=erOs4H4BXOA




this uses custom voice commands for asterisk. I use the google text to speech for this. I'm not sure i can share the .gsm files. 

here is a command to generate .gsm files using  gtts-cli (install with pip) and sox
```
 gtts-cli 'Dear caller this is a message' | sox -t mp3 -  -r 8000 -c1 message.gsm
```
