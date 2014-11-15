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

// includes
include('inc/config.php');
include('inc/functions.php');

// not every server setting allows this!!
set_time_limit(0);     // set maximum execution time to 0 (endless)

// debug mode; set $html to 0 if you start the masher from command-line(php-cli)
$html = "1";
if($html == "1") {
  echo "<html><head><title>Debug Mode</title></head><body bgcolor=\"\#000000\" text=\"\#ffffff\"><pre>";
  echo "IRCmasher started. Have a lot of fun ... \n";
}
// include all modules
$modules = array("");
$moddir = opendir ("modules/");
echo "Modules loaded: \n";
while ($file = readdir($moddir)) {
  if (eregi(".php", $file)) {
    echo $file . "\n";
    include("modules/" . $file);
    $file = str_replace(".php","",$file);
    array_push($modules, $file);
  }
}
echo "\n";
closedir($moddir);

// logging data
#if($logdata == "1") {
#  $logh = fopen("ircmasher.log","a");
#}

// uptime data
$uptime_data = "modules/data/uptime.data";
$timestamp = time();
uptime($timestamp, $uptime_data);

// connect to the server
global $ircsocket;
$ircsocket = fsockopen($server, $port, &$errno, &$errstr, 5);

if (!$ircsocket) {
  die("Error connecting to host.");
}

// login
if($pass != "") {
  write_socket("PASS ". $pass);
}
write_socket("NICK ". $nick);
write_socket("USER ". $nick. " 0 0 :". $real_name);
sleep(1);

// main function
  
while (!feof($ircsocket)) {
    
  $incoming = fgets($ircsocket, 1024);
  
  // split $incoming
  $output = explode(":",$incoming);
  $com1 = $output[0];
  $com2 = $output[1];
  $com3 = $output[2];
  $com4 = $output[3];

  // play PING PONG with the irc server
  if($output[0] == "PING ") {
    write_socket("PONG " . $output[1]);
  }

  // if username is in use
  if (eregi("Nickname is already in use.", $com3)) {
    write_socket("NICK ". $nick_alternate);
    write_socket("USER ". $nick_alternate. " 0 0 :". $real_name);
    sleep(1);
  }

  // identify
  if ($ident != "" && eregi("End of /MOTD command.", $com3)) {
    priv_msg($nickserv, "IDENTIFY $ident");
  }
  
  // join channels
  if (eregi("End of /MOTD command.",$output[2]) || eregi("End of MOTD command.",$output[2])) {
    $channels = explode(";", $channel);
    foreach($channels as $channels) {
      write_socket("JOIN $channels");
      sleep(1);
    }
  }
  
  // commands after connection is established
  // - on join say hello - 
  $on_join = explode(" ", $com2);
    if($on_join[1] == "332") {
      delay_priv_msg($on_join[3], $hello, "5");
  }   
  
  // split some further variables
  $infos = explode("!~", $output[1]);
  $name = $infos[0];
  $begin = explode(" ", $output[1]);
  $chan = $begin[2];
  $command = $begin[1];

  // create $message
  $message = $output[2];
  #$i = 0;
  #foreach($output as $var) {
  #  if ($i > 2) {
  #    $message = $message . $var;
  #  }
  #  $i++;
  #}

  
  
  // now insert the modules
  foreach ($modules as $exec_func) {
    if(function_exists($exec_func)) {
      call_user_func($exec_func, $incoming, $com1, $com2, $com3, $com4, $name, $begin, $chan, $command, $message);
    }  
  }

  // rejoin
  if($rejoin == "1") {
    if(eregi("KICK", $output[1])) {
      $kicked_channel = explode(" ", $output[1]);
      write_socket("JOIN " . $kicked_channel[2]);
    }
  }

  // quit the bot
  if (eregi("go to bed $nick $botpw",$message)) {
    priv_msg($chan, $quit_message);
    fputs($ircsocket, "QUIT : Life is too short\n\r");
    fclose($ircsocket);
    $timestamp = "";
    uptime($timestamp, $uptime_data);
    die('Bot down.</pre>');
  }

  echo $incoming;  // debuging information 
}

if($html == "1") {
  echo "</pre><body></html>";
}
?>
