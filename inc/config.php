<?php
/**************************************************
 *
 * IRCsmasher - based on IRCmasher by Ralf Oechsner and Volker Latainski
 *
 * @Project: IRCsmasher
 * @License: GNU General Public License v2
 *
 **************************************************/
// make variables global
global $server, $port, $pass, $nick, $real_name, $channel, $botpw;

// Global configuration
$server = "irc.example.com";    // IRC server
$port = "6667";                 // IRC server port
$pass = "";                     // IRC server password (leave blank if not required)
$nick = "ircsmasher";           // The bot's nickname
$nick_alternate = "ircmasher";  // Alternate nick in case $nick is in use
$real_name = "IRCsmasher";      // Bot's "real" name
$channel = "#ircsmasher";       // Channels to join (seperate multiple channels with ';')
$botpw = "badpassword";         // Bot password
$rejoin = "1";                  // Enable auto rejoin? 1 = yes; 0 = no;
$strokes = "300";               // Bot's "typing speed" in strokes per minute
$nickserv = "NickServ";         // NickServ
$ident = "";                    // ident password (leave blank if not needed)

//Debugging
$debug_mode = "on";             // Enables / Disables outputting debugging information (on/off)
$debug_html = "1";              // debug mode - set to 0 for if running from cli, 1 if you want html output

// personal configuration
$hello = "Hello @ All";         // channel join message
$quit_message = "cu @ all";      // message when bot shuts down
$log_uptime = "on";             // Enable logging of bot's uptime (on/off)
?>
