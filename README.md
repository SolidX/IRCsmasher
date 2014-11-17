IRCsmasher
==========

A fork of the [IRCmasher](http://sourceforge.net/projects/ircmasher/) IRC bot project by by Ralf Oechsner and Volker Latainski.


Disclaimer
----------

Use this bot at your own risk. We don't guarantee it works all the time... or ever.

Installation & Use
------------------

###Webserver
Drop the source files in to an accessible path on your web server and visit index.php.
You should protect this script with a password or something. See your web server's manual for more information!


###Command Line
Alternatively, you can run this from the command-line (DOS-Prompt, Shell, ect.).
This requires PHP with CLI-Support! (See [Using PHP from the command line](http://www.php.net/manual/en/features.commandline.php))

Configuration
-------------

Edit the values in the [config file](inc/config.php).

Modules
-------

You can extend the functionality of your IRCsmasher by adding modules to it's `modules` directory.
All modules placed in the directory are automatically loaded and run.
To create a new module see [modules/basic.php](modules/basic.php) as an example.

Commands
--------
Below are some examples of commands you can run out of the box.

NOTE: It is highly recommended that you query the bot directly to perform admin tasks - but you can also perform them by messaging a channel the bot is in.
 
```
// mute/unmute the bot...
mute $botpasswd $channel               (mute the bot)
unmute $botpasswd $channel             (unmute the bot)

// Admin commands
giveop $botpw $channel $nick           (give chan-op status)
takeop $botpw $channel $nick           (take chan-op status)
give voice                             (voice a user)
dvoice $botpw $channel $nick           (de-voice a user)
kick $botpw $channel $nick             (kick a user from channel)
say $botpw $your_message_txt           (let the bot talk)
go to bed $nick $botpw                 (disconnect)

// Common triggers...
!time                                  (the time...)
!date                                  (the date...)
!uptime                                (returns the bot uptime)
!peak                                  (returns the peak number of users and when it occurred for a given channel)
```

License
-------
[GNU General Public License v2](LICENSE)

Bugs, Contact, Comments
-----------------------

Questions? Bugs? Suggestions or comments? Feel free to jump in!
