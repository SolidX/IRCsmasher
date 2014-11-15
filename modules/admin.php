<?php
/**************************************************
 *
 * IRCmasher
 * [http://sourceforge.net/projects/ircmasher/]
 *
 * @Project: IRCmasher
 * @Authors: Ralf Oechsner, Volker Latainski
 * @License: GNU General Public License
 *
 **************************************************
 *
 * $id$
 *
 **************************************************/

function admin ($output, $com1, $com2, $com3, $com4, $name, $begin, $chan, $command, $message) {

  global $ircsocket, $server, $port, $nick, $channel, $real_name, $botpw, $ident, $nickserv; 
  
  //split message into single words
  $arguments = explode(" ",$message);

  if ($arguments[0] == "giveop" && $arguments[1] == $botpw && $command == "PRIVMSG") {
    write_socket("MODE $arguments[2] +o $arguments[3]");
  }

  if ($arguments[0] == "takeop" && $arguments[1] == $botpw && $command == "PRIVMSG") {
    write_socket("MODE $arguments[2] -o $arguments[3]");
  }

  if ($arguments[0] == "voice" && $arguments[1] == $botpw && $command == "PRIVMSG") {
    write_socket("MODE $arguments[2] +v $arguments[3]");
  }

  if ($arguments[0] == "dvoice" && $arguments[1] == $botpw && $command == "PRIVMSG") {
    write_socket("MODE $arguments[2] -v $arguments[3]");
  }
  
  if ($arguments[0] == "kick" && $arguments[1] == $botpw && $command == "PRIVMSG") {
    write_socket("KICK $arguments[2] $arguments[3]");
  }
  
  if ($arguments[0] == "identify" && $arguments[1] == $botpw && $command == "PRIVMSG") {
    write_socket("NICK $nick");
    write_socket("PRIVMSG $nickserv IDENTIFY $ident");
  }

  //let the bot talk for you :-)
  if ($arguments[0] == "say" && $arguments[1] == $botpw && $command == "PRIVMSG") {
      $message = str_replace("say","",$message);
      $message = str_replace("$botpw","",$message);
#     $message = str_replace("$arguments[2]","",$message);
      priv_msg($channel, ltrim($message));   
  }
  
#  if ($arguments[0] == "restart" && $arguments[1] == $botpw && $command == "PRIVMSG") {
    #doesn't work yet
#  }
}
?>
