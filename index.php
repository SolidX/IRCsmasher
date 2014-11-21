<?php
/**************************************************
 *
 * IRCsmasher - based on IRCmasher by Ralf Oechsner and Volker Latainski
 *
 * @Project: IRCsmasher
 * @License: GNU General Public License v2
 *
 **************************************************/

// includes
require_once('inc/config.php');
require_once('inc/functions.php');

set_time_limit(0); //set unlimited maximum execution time
$enable_debugging = ($debug_mode == "on");

if ($enable_debugging) {
    if ($debug_html == "1")
        echo "<html><head><title>IRCsmasher Debug Mode</title></head><body bgcolor=\"\#000000\" text=\"\#ffffff\"><pre>";
    echo "IRCmasher started. Have a lot of fun ... \n";
}

// include all modules
$modules = array();
$moddir = opendir("modules/");

if ($enable_debugging)
    echo "Modules loaded: \n";

while ($file = readdir($moddir)) {
    $file_info = pathinfo($file);
    if (isset($file_info['extension']) && $file_info['extension'] == "php" && $file_info['filename'] != "BaseBotModule") {
        if ($enable_debugging)
            echo $file . "\n";
        
        include_once("modules/" . $file);
        $file = str_replace(".php", "", $file);
        
        global $ircsocket;
        $ircsocket = null; //Socket placeholder

        //Pseudo-module-factory
        if (class_exists($file)) {
            //Module must have the same filename and class name for this to work
            $module = new $file($ircsocket, $server, $port, preg_quote($nick), $channel, $real_name, $botpw); 
            array_push($modules, $module);
        }
    }
}
closedir($moddir);
if ($enable_debugging)
    echo "\n";

// uptime data
$uptime_data = "modules/data/uptime.data";
$timestamp = time();
uptime($timestamp, $uptime_data);

// connect to the server
global $ircsocket;
$ircsocket = fsockopen($server, $port, $errno, $errstr, 5);

if (!$ircsocket) {
    die("Error connecting to host.");
}

// login
if ($pass != "")
    authenticate($pass);
register_connection($nick, $real_name);

// main function
while (!feof($ircsocket)) {

    $incoming = fgets($ircsocket, 1024);

    // split $incoming
    $output = explode(":", $incoming);
    $com1 = $output[0];
    $com2 = $output[1];
    $com3 = $output[2];
    $com4 = $output[3];

    // play PING PONG with the irc server
    if ($output[0] == "PING ") {
        write_socket("PONG " . $output[1]);
    }

    // if username is in use
    if (preg_match("/Nickname is already in use\./i", $com3))
        register_connection($nick_alternate, $real_name);

    // identify
    if ($ident != "" && preg_match("/End of \/MOTD command\./i", $com3)) {
        identify($nickserv, $ident);
    }

    // join channels
    if (preg_match("/End of \/MOTD command\./i", $output[2]) || preg_match("/End of MOTD command\./i", $output[2])) {
        $channels = explode(";", $channel);
        foreach ($channels as $room) {
            join_channel($room);
        }
    }

    // commands after connection is established
    // - on join say hello - 
    $on_join = explode(" ", $com2);
    if ($on_join[1] == "332") {
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

    // now execute the modules
    foreach ($modules as $exec_module) {
        if (preg_match("/!triggers/i", $message))
            $exec_module->getTriggers($name);
        else
            $exec_module->runModule($incoming, $com1, $com2, $com3, $com4, $name, $begin, $chan, $command, $message);
    }

    // rejoin
    if ($rejoin == "1") {
        if (preg_match("/KICK/", $output[1])) {
            $kicked_channel = explode(" ", $output[1]);
            join_channel($kicked_channel[2]);
        }
    }

    // quit the bot
    if (preg_match("/go to bed ".preg_quote($nick." ".$botpw)."/i", $message)) {
        quit($quit_message);
        fclose($ircsocket);
        $timestamp = "";
        uptime($timestamp, $uptime_data);
        die('Bot down.</pre>');
    }

    if ($enable_debugging)
        echo $incoming;  // echos raw input from server
}

if ($enable_debugging && $debug_html == "1") {
    echo "</pre><body></html>";
}
?>
