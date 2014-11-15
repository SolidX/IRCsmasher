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
$server = "irc.euirc.net";       // the ircserver 
$port = "6667";                  // port of the irc server
$pass = "";                      // leave empty if server doesn't require a pw
$nick = "ircsmasher";             // the nickname of the bot
$nick_alternate = "weiberheld";  // nick if first is in use
$real_name = "ircsmasher";       // his real name
$channel = "#ircsmasher";         // seperate multiple channels with ';'
$botpw = "12345";           // password for the bot
$rejoin = "1";                     // activate rejoin? 1 = yes; 0 = no;
$strokes = "300";                  // strokes a minute, speed of the bot
$nickserv = "NickServ";           // NickServ 
$ident = "";                // ident password if you don't need leave empty

// personal configuration
$hello = "Hello @ All";          // channel join message
$quit_message = "cu @all";       // message when bot shuts down
$log_uptime = "on";              // log mashers uptime - on/off
?>
