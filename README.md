IRCsmasher
==========

A fork of the [IRCmasher](http://sourceforge.net/projects/ircmasher/) IRC bot project by by Ralf Oechsner and Volker Latainski.

The original intent of this bot was to create a lightweight, easy to deploy IRC bot that didn't rely on a database. However, it's always nice to have options, so feel free to extend this to use one.


Installation & Use
------------------

###Webserver
Drop the source files in to an accessible path on your web server and visit index.php.
You should protect this script with a password or something otherwise anyone who visits will be able to launch your IRC bot.
See your web server's manual for more information!

###Command Line
Alternatively, you can run this from the command-line (DOS-Prompt, Shell, ect.).
This requires PHP with CLI-Support! (See [Using PHP from the command line](http://www.php.net/manual/en/features.commandline.php))
(You may want to set `debug_output_html` to `Off` in the [config file](inc/config.ini) if you do this.)


Configuration
-------------

Edit the values in the [config file](inc/config.ini). 


Modules
-------

You can extend the functionality of your IRCsmasher by adding modules to it's `modules` directory.
All modules placed in the directory are automatically loaded and run at run time.
To create a new module see [modules/basic.php](modules/basic.php) as an example.


Commands
--------
Below are some examples of commands you can run out of the box.

NOTE: It is highly recommended that you private message the bot directly to perform administrative tasks - but you can also perform them by messaging any channel the bot is in.
 
```
//AI module
!mute <bot_password>                                            (mute AI module)
!unmute <bot_password>                                          (unmute AI module)

//Admin commands
giveop <bot_password> <target_channel> <target_user>            (give chan-op status)
takeop <bot_password> <target_channel> <target_user>            (take chan-op status)
give voice <bot_password> <target_channel> <target_user>        (voice a user)
dvoice <bot_password> <target_channel> <target_user>            (de-voice a user)
join <bot_password> <target_channel>                            (have bot join the specified channel)
kick <bot_password> <target_channel> <target_user>              (kick a user from channel)
say <user_or_channel> <bot_password> <your_message>             (Have the bot say something to a user or channel)
go to bed <bots_nick> <bot_password>                            (shutdown the bot)

//Common triggers...
!time                                                           (display the bot's current time)
!date                                                           (display the bot's current date)
!uptime                                                         (returns the bot's uptime)
!peak                                                           (returns the peak number of users and when it occurred for a given channel)
```

License
-------
[GNU General Public License v2](LICENSE)

Bugs, Contact, Comments
-----------------------

Questions? Bugs? Suggestions or comments? Feel free to jump in and create issues or pull requests!
