IRCsmasher
==========

A fork of the IRCmasher IRC bot hosted at http://sourceforge.net/projects/ircmasher/


Disclaimer
----------

We are not liable for any damage caused by the use of this program.

Installation
------------

Just put it on your webserver and point a browser to the script.
You should protect the script with a password (htaccess).
See your webserver's manual!

You can use it alternatively from the command-line (DOS-Prompt, Shell, ect.).
This requires PHP with CLI-Support! (See [Using PHP from the command line](http://www.php.net/manual/en/features.commandline.php))

Configuration
-------------

Edit the file (./inc/config.php). Should be self explanatory.

Modules
-------

Put the modules you want to have started with IRCsmasher in the 'modules' directory.

Commands
--------

NOTE: We suggest to querying the bot directly for admin tasks - but it works by messaging the channel fine. 
 
```
// mute/unmute the masher...
mute $botpasswd $channel               (mute the bot)
unmute $botpasswd $channel             (unmute the bot)

// the admin commands
giveop $botpw $channel $nick           (give chan-op status)
takeop $botpw $channel $nick           (take chan-op status)
give voice                             (voice a user)
dvoice $botpw $channel $nick           (de-voice a user)
kick $botpw $channel $nick             (kick a user from channel)
say $botpw $your_message_txt           (let the bot talk)
go to bed $nick $botpw                 (disconnect)

// the user commands...
!time                                  (the time...)
!date                                  (the date...)
!uptime                                (returns the bot/script uptime)
!peak                                  (returns the channel peak and time/date it was set)
```

License
-------

See LICENSE.


Bugs, Contact, Comments
-----------------------

Any questions? Any bugs? Go to http://ircmasher.sourceforge.net
