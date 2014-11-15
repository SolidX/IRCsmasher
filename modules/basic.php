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

function basic ($output, $com1, $com2, $com3, $com4, $name, $begin, $chan, $command, $message) {

  global $ircsocket, $server, $port, $nick, $channel, $real_name, $botpw; 

  //if you say schnarchnase
  if (eregi("schnarchnase", $message)) {
    delay_priv_msg($channel, "schnarchnasen sind cool!", 2);
  }
 
  //and now the masters ;)
  if (eregi("whois maniacbrain",$message)) {
    priv_msg($chan, "maniacbrain is one of my masters and creators");
  }
  if (eregi("whois rtf", $message)) {
    priv_msg($chan, "rtf is one of my masters and creators");
  }
  if (eregi("whois ircmasher", $message)) {
    priv_msg($chan, "ircmasher is the greatest masher in the hole irc ;)");
  }  
  
  //whatis words
  if (eregi("whatis rtfm", $message)) {
    priv_msg($chan, "rtfm = read the fuckin manual!!!");
  }
  if (eregi("whatis stfw", $message)) {
    priv_msg($chan, "stfw = search the fuckin web!!!");
  }  
  
  //entries of the old linux.php
  if (eregi("whatis linux", $message)) {
    priv_msg($chan, "Linux is a free Unix-type operating system originally created by Linus Torvalds with the assistance of developers around the world...and it's the best operating system in the world :-)");
  }
  if (eregi("whatis gnu", $message)) {
    priv_msg($channel, "GNU is a recursive acronym for \"GNU's Not Unix\"; it is pronounced \"guh-noo\".");
  } 
  if (eregi("paragraph", $message)) {
    priv_msg($chan, "§1. Ich habe immer Recht! §2. Sollte ich einmal nicht Recht haben so tritt automatisch §1 in Kraft...");
  }

  //time/date actions
  if (eregi("!time", $message)) {
    $today = getdate();
    $time = date("H:i:s");
    delay_priv_msg($chan, "$time", 2);
  }     
  
  if (eregi("!date", $message)) {
    $today = getdate();
    $date = date("D M j G:i:s T Y"); 
    delay_priv_msg($chan, "$date", 2);
  }    
  
  //uptime action
  if (eregi("!uptime", $message)) {
    $uptime_data = "modules/data/uptime.data";
    $handle = fopen($uptime_data, "r"); 
    $timestamp_old = fread($handle, filesize($uptime_data));
    fclose($handle); 
    $timestamp_new = time();
    $timestamp_diff = $timestamp_new - $timestamp_old;
      
    $dd = $timestamp_diff / (24*60*60);
    list ($dd_vk, $dd_nk) = explode('.', $dd);
    $dd = $dd_vk;
    
    $hh1 = "0." . $dd_nk; 
    $hh2 = $hh1 * 24;
    list ($hh_vk, $hh_nk) = explode('.', $hh2);
    $hh = $hh_vk;
    
    $mm1 = "0." . $hh_nk; 
    $mm2 = $mm1 * 60;
    list ($mm_vk, $mm_nk) = explode('.', $mm2);
    $mm = $mm_vk;
    
    $ss1 = "0." . $mm_nk;
    $ss2 = $ss1 * 60;
    list ($ss_vk, $ss_nk) = explode('.', $ss2);
    $ss = $ss_vk;

    $uptime_is = "I'm up for " . $dd . " days, " . $hh 
    . " hours, " . $mm . " minutes and " . $ss . " seconds";
    delay_priv_msg($chan, $uptime_is,2);
  }         

}
?>
