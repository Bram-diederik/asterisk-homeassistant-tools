# asterisk-homeassistant-tools

# sascha 
asterisk number recognition, smart voicemail

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
`apt install php php-yaml php-curl sox libsox-fmt-all xmlstarlet php-mysql python3-gtts translate-shell`

create a gsm tts message with gtts
```
gtts-cli 'Dear caller this is a message' | sox -t mp3 -  -r 8000 -c1 message.gsm
```

crontab
```
*  *  *   *   *      /opt/sascha/asterisk/trigger.php  > /dev/null 2>&1
*  *  *   *   *  /opt/asterisk/parse_voicemail.php > /dev/null 2>&1
*  *  *   *   * /opt/sascha/wakeup/start_wakeup_call.php > /dev/null 2>&1
*/10 * *  *   * /opt/sascha/nextcloud/update_calendar.php > /dev/null 2>&1
10  0  *   *   *   /opt/sascha/nextcloud/import_phonebook.php > /dev/null 2>&1
20  0  *   *   *   /opt/sascha/ldap/push_ldap.php > /dev/null 2>&1
```
