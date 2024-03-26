For Ollama call screen prompt. 

Set up a seperate home assistant instance. (call text typed into the  assistant is processed by this function so it blocks everyting on a normal system)

create 2 ollama interfaces. (use a diffrent port for the 2nd one). with openhermes (doing the best job)

allow prompt

```
YOU ARE A part of a CALL ACCEPTANCE SYSTEM always reply with reply (rule) or deny. No  Explanation of steps and reasoning 
they can call me Bram, Brum, ram or brom
You are given the subject (English or dutch translate the text to english if dutch) of a phone call apply the rules listed. only reply with allow [rule letter] or deny. If a rule is matched stop processesing. 
there might be spelling errors in the subject translate those before doing anything else [deze = dit is,gevald = gevallen]
always reply with allow or deny

family and friends: henk,gerard,ingrid
RULES:
b) Allow calls (aslong not provoking) from the following medical services like ambulance or hospital (dutch: ziekenhuis,huisarts), police (dutch: politie), or firefighters (dutch: brandweer). Reply with: "Allow b".
e) Allow any call with the name my commpany. reply with allow e.
d) Allow calls from family members like vader or moeder reply with: "Allow d".
{% if states('calendar.persoonlijk') == 'on' or state_attr('calendar.persoonlijk', 'start_time') is string %}
    {% set meeting_start_time = as_timestamp(state_attr('calendar.persoonlijk', 'start_time')) %}
    {% set current_time = as_timestamp(now()) %}
    
    {% if (meeting_start_time - 3600 < current_time) and (meeting_start_time > current_time) %}
        f) You are having a meeting/appointment: {{ state_attr('calendar.persoonlijk','message') }} reply with "Allow f" for calls that are related with this meeting.
    {% elif meeting_start_time <= current_time %}
        f) You are having a meeting/appointment: {{ state_attr('calendar.persoonlijk','message') }} reply with "Allow f" for calls that are related with this meeting.
    {% endif %}
{% endif %}
g) Allow any call of (aslong not provoking) emergency (brand/fire) or accedents dutch (auto) ongeluk. expecially about family and friends. reply with: "Allow g"
h) allow call of a taxi,cap?
i) Deny the call for any other subject if you don't know. This is the safest bet. Reply with: Deny
```


use the following automation.

to set your right agent_id cut the template select the right agent and past the template again
      

badmouth prompt

```
check if a harming or provoking sentence is made (dont trigger just on a single word like moeder or mother)
reply with YES if a proving text is found or NO if not. After that YES/NO a brief reason why in {{ states('input_text.sara_language') }} language with less than 9 words.
```


```
alias: voip grant
description: ""
trigger:
  - platform: conversation
    command:
      - "{query}"
condition: []
action:
  - service: input_text.set_value
    metadata: {}
    data:
      value: "{{trigger.slots.query}}"
    target:
      entity_id: input_text.sara_recieved_message
  - service: shell_command.sara_wait
    metadata: {}
    data: {}
  - service: input_datetime.set_datetime
    metadata: {}
    data:
      datetime: "{{ now().strftime(\"%Y-%m-%d %H:%M:%S\") }}"
    target:
      entity_id: input_datetime.sara_time_wait
  - wait_template: "{{ states('input_boolean.sara_busy') == 'off' }}"
    continue_on_timeout: true
    timeout: "40"
  - service: input_boolean.turn_on
    metadata: {}
    data: {}
    target:
      entity_id: input_boolean.sara_busy
  - service: input_datetime.set_datetime
    metadata: {}
    data:
      datetime: "{{ now().strftime(\"%Y-%m-%d %H:%M:%S\") }}"
    target:
      entity_id: input_datetime.sara_time_wait_stop
  - service: input_datetime.set_datetime
    metadata: {}
    data:
      datetime: "{{ now().strftime(\"%Y-%m-%d %H:%M:%S\") }}"
    target:
      entity_id: input_datetime.sara_time_allow
  - service: conversation.process
    metadata: {}
    data:
      agent_id: 6a1a09321e9c62297785c897c39c340b
      text: "{{trigger.slots.query}}"
    response_variable: return
    enabled: true
  - service: input_boolean.turn_off
    target:
      entity_id:
        - input_boolean.sara_busy
    data: {}
  - service: input_datetime.set_datetime
    metadata: {}
    data:
      datetime: "{{ now().strftime(\"%Y-%m-%d %H:%M:%S\") }}"
    target:
      entity_id: input_datetime.sara_time_allow_stop
  - choose:
      - conditions:
          - condition: template
            value_template: "{{ 'ALLOW' in return.response.speech.plain.speech | upper }}"
        sequence:
          - service: shell_command.sara_allow
            metadata: {}
            data: {}
          - service: tts.speak
            metadata: {}
            data:
              cache: true
              media_player_entity_id: media_player.leontien
              language: nl
              message: "inkomend gesprek: {{trigger.slots.query}}"
            target:
              entity_id: tts.google_en_com
          - set_conversation_response: |-
              {{ return.response.speech.plain.speech}}
                        wait: {{ (states('input_datetime.sara_time_wait_stop') | as_timestamp - (states('input_datetime.sara_time_wait') | as_timestamp)) }}
                        check: {{ (states('input_datetime.sara_time_allow_stop') | as_timestamp - (states('input_datetime.sara_time_allow') | as_timestamp)) }}
                    
      - conditions:
          - condition: template
            value_template: "{{ 'DENY' in return.response.speech.plain.speech | upper}}"
        sequence:
          - service: input_datetime.set_datetime
            metadata: {}
            data:
              datetime: "{{ now().strftime(\"%Y-%m-%d %H:%M:%S\") }}"
            target:
              entity_id: input_datetime.sara_time_badmouth
          - service: conversation.process
            metadata: {}
            data:
              agent_id: 379034234069a1b258e2fdb50065c748
              text: "{{trigger.slots.query}}"
            response_variable: badmouth
          - service: input_datetime.set_datetime
            metadata: {}
            data:
              datetime: "{{ now().strftime(\"%Y-%m-%d %H:%M:%S\") }}"
            target:
              entity_id: input_datetime.sara_time_badmouth_stop
          - choose:
              - conditions:
                  - condition: template
                    value_template: >-
                      {{ ('YES' in
                      badmouth.response.speech.plain.speech.split()[0].upper())
                      or ('JA' in
                      badmouth.response.speech.plain.speech.split()[0].upper() )
                      }}
                sequence:
                  - service: input_text.set_value
                    metadata: {}
                    data:
                      value: >-
                        dit is een provocerende zin. This sentence is
                        provocative.
                    target:
                      entity_id: input_text.sara_badmouth
                  - service: input_text.set_value
                    metadata: {}
                    data:
                      value: >-
                        {% set original_string =
                        badmouth.response.speech.plain.speech %} {% set
                        first_word = original_string.split()[0] %} {% set
                        cleaned_string = original_string.split(first_word,
                        1)[1].strip() | replace('!','') | replace('@','') |
                        replace('#','') | replace('$','') | replace('%','') |
                        replace('^','') | replace('&','') | replace('*','') |
                        replace('(','') | replace(')','') | replace('-','') |
                        replace("'",'') | replace('"','')| replace('\n',' ') %}
                        {{ cleaned_string }}
                    target:
                      entity_id: input_text.sara_badmouth
                  - service: shell_command.sara_block
                    metadata: {}
                    data: {}
                  - set_conversation_response: |-
                      banned: {{ states('input_text.sara_badmouth') }}
                                wait: {{ (states('input_datetime.sara_time_wait_stop') | as_timestamp - (states('input_datetime.sara_time_wait') | as_timestamp)) }}
                                check: {{ (states('input_datetime.sara_time_allow_stop') | as_timestamp - (states('input_datetime.sara_time_allow') | as_timestamp)) }}
                                badmouth: {{ (states('input_datetime.sara_time_badmouth_stop') | as_timestamp - (states('input_datetime.sara_time_badmouth') | as_timestamp)) }}
            default:
              - service: shell_command.sara_deny
                data: {}
              - set_conversation_response: |-
                  geweigert
                            wait: {{ (states('input_datetime.sara_time_wait_stop') | as_timestamp - (states('input_datetime.sara_time_wait') | as_timestamp)) }}
                            check: {{ (states('input_datetime.sara_time_allow_stop') | as_timestamp - (states('input_datetime.sara_time_allow') | as_timestamp)) }}
                            badmouth: {{ (states('input_datetime.sara_time_badmouth_stop') | as_timestamp - (states('input_datetime.sara_time_badmouth') | as_timestamp)) }}
    default:
      - if:
          - condition: template
            value_template: >-
              {{ "politie" in trigger.slots.query |  lower   }}
        then:
          - service: shell_command.sara_allow
            metadata: {}
            data: {}
          - service: tts.speak
            metadata: {}
            data:
              cache: true
              media_player_entity_id: media_player.leontien
              language: nl
              message: "inkomend gesprek: {{trigger.slots.query}}"
            target:
              entity_id: tts.google_en_com
          - set_conversation_response: toegestaan
        else:
          - service: shell_command.sara_deny
            metadata: {}
            data: {}
          - set_conversation_response: geweigert
        enabled: true
  - choose:
      - conditions:
          - condition: template
            value_template: "{{trigger.slots.query|upper == \"ALLOW\" }}"
        sequence:
          - set_conversation_response: deny
          - stop: ""
mode: single

```

script to load the model for faster responce.

```
alias: keep alive
sequence:
  - wait_template: "{{ states('input_boolean.sara_busy') == 'off' }}"
    continue_on_timeout: true
  - service: input_boolean.turn_on
    target:
      entity_id:
        - input_boolean.sara_busy
    data: {}
  - service: conversation.process
    metadata: {}
    data:
      agent_id: 6a1a09321e9c62297785c897c39c340b
      text: dit is de poltitie
  - service: input_boolean.turn_off
    metadata: {}
    data: {}
    target:
      entity_id: input_boolean.sara_busy
mode: single
```
