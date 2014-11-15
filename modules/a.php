<?php
/**************************************************
 *
 * IRCsmasher - based on IRCmasher by Ralf Oechsner and Volker Latainski
 *
 * @Project: IRCsmasher
 * @License: GNU General Public License v2
 *
 **************************************************/

$module_name = "ai";
$datapath = "modules/data/";
$module_src = $datapath . $module_name . ".data";
$db = file($module_src);
#$iq_status = "1";

function iq_function ($name, $begin, $chan, $command, $message) {

  global $module_src, $ircsocket, $server, $port, $nick, $channel, $real_name, $botpw, $db;

  for($x=0;$x<count($db);$x++) {
    list ($a, $b) = split('[|]',$db[$x]);
    if (eregi("$a", $message) && $command == "PRIVMSG") {
      if (substr($b,0,7) == "/ACTION") {
	$action = str_replace("/ACTION ","",$b); //don't put '/ACTION' out                                             
	$action = rtrim($action);  //delete useless signs on the right                                                 
	priv_msg($chan, "\001ACTION " .  $action . " \001");
      }
      else {
	priv_msg($chan, "$b");
      }
    }
  }

}

function a ($output, $com1, $com2, $com3, $com4, $name, $begin, $chan, $command, $message) {

  global $module_src, $ircsocket, $server, $port, $nick, $channel, $real_name, $botpw, $db, $iq_status; 
     
  $arguments = explode(" ",$message);  //split message into words

  /*if ($arguments[0] == "refresh_db" && $arguments[1] == $botpw && $command == "PRIVMSG") {
    fclose($db);  //close db
    unset($db);
    $db = file($module_src);  //and read it again
  }*/

  if ($arguments[0] == '!mute' && $arguments[1] == $botpw && $command == "PRIVMSG") {
    $iq_status = "0";
    priv_msg($chan,"I am quiet now.");
  }

  if ($arguments[0] == '!unmute' && $arguments[1] == $botpw && $command == "PRIVMSG") {
    $iq_status = "1";
  }

  if (!isset($iq_status) || $iq_status != "0") {
    iq_function($name, $begin, $chan, $command, $message);
  }
}
?>
