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
require_once('logger/SimpleLogger.php');
require_once('logger/HTMLLogger.php');

set_time_limit(0); //set unlimited maximum execution time

//Start up logger
if ($debug_html == "1")
    $log = new HTMLLogger();
else
    $log = new SimpleLogger();

if (($debug_mode == "on"))
    $log->enable_logging();

$log->write(Logger::LOGMSG_INFO, "IRCmasher started. Have a lot of fun...");

// include all modules
$modules = array();
$moddir = opendir("modules/");

if ($moddir) { //Ensure modules dir was loaded
    $log->write(Logger::LOGMSG_INFO, "Modules loaded:");

    while ($file = readdir($moddir)) {
        $file_info = pathinfo($file);
        if (isset($file_info['extension']) && $file_info['extension'] == "php" && $file_info['filename'] != "BaseBotModule") {
            $log->write(Logger::LOGMSG_INFO, "\t{$file}");

            include_once("modules/" . $file);
            $file = str_replace(".php", "", $file);

            global $ircsocket;
            $ircsocket = null; //Socket placeholder

            //Pseudo-module-factory
            if (class_exists($file)) {
                //Module must have the same filename and class name for this to work
                $module = new $file($ircsocket, $server, $port, preg_quote($nick), $channel, $real_name, $botpw, $log); 
                array_push($modules, $module);
            }
        }
    }
    closedir($moddir);
    $log->write(Logger::LOGMSG_INFO, "\n");
} else {
    $log->write(Logger::LOGMSG_ERROR, "Error loading modules directory.");
    exit(2);
}

// uptime data
$uptime_data = "modules/data/uptime.data";
$timestamp = time();
uptime($timestamp, $uptime_data);

// connect to the server
global $ircsocket;
$ircsocket = fsockopen($server, $port, $errno, $errstr, 5);

if (!$ircsocket) {
    $log->write(Logger::LOGMSG_ERROR, "Error connecting to IRC server host.");
    exit(1);
}

// login
if ($pass != "")
    authenticate($pass);
register_connection($nick, $real_name);

// main function
while (!feof($ircsocket)) {
    $incoming = fgets($ircsocket, 1024);

    $log->write(Logger::LOGMSG_INFO, $incoming); // echos raw input from server

    // Split $incoming
    $output = explode(":", $incoming, 3);
    $com1 = $output[0]; //Prefix
    $com2 = isset($output[1]) ? $output[1] : ""; //Command
    $com3 = isset($output[2]) ? $output[2] : ""; //Params
    
    // play PING PONG with the irc server
    if ($com1 == "PING ") {
        pong($com2);
        continue;
    }

    // Parse Command
    $cmd_com = explode(" ", $com2); //[0] - Originator, [1] Command / Status, [2+] (optional)
    if (count($cmd_com) < 2)
        continue; //Surpress ghosts in the connection. I don't know why this works :/
    
    // if username is in use
    if ($cmd_com[1] == "433") {
        register_connection($nick_alternate, $real_name);
        continue;
    }

    // if end of MOTD
    if ($cmd_com[1] == "376")
    {
        // identify
        if ($ident != "")
            identify($nickserv, $ident);

        // join default channels
        $channels = explode(";", $channel);
        foreach ($channels as $ch)
            join_channel($ch);
        
        continue;
    }

    // commands after connection is established
    // - on join say hello - 
    if ($cmd_com[1] == "332") {
        delay_priv_msg($cmd_com[3], $hello, "5");
        continue;
    }
    
    // split some further variables
    // parse message sender's identity
    $sender_nick = null;
    $sender_user = null;
    $sender_host = null;
    
    $msg_originator = $cmd_com[0];
    $msg_originator_components = explode("!", $msg_originator, 2);
    if (count($msg_originator_components) > 1) {
        $sender_nick = $msg_originator_components[0];
        
        $msg_originator_components = explode("@", $msg_originator_components[1], 2);
        if (count($msg_originator_components) > 1) {
            $sender_user = $msg_originator_components[0];
            $sender_host = $msg_originator_components[1];
        }
    }
    
    $chan = $cmd_com[2];
    $command = $cmd_com[1];

    // obtain $message
    $message = $com3;

    // now execute the modules
    foreach ($modules as $exec_module) {
        if (preg_match("/!triggers/i", $message))
            $exec_module->getTriggers($sender_nick);
        else
            $exec_module->runModule($incoming, $com1, $com2, $com3, $sender_nick, $cmd_com, $chan, $command, $message);
    }

    // rejoin
    if ($rejoin == "1") {
        if ($cmd_com[1] == "KICK") {
            join_channel($cmd_com[2]);
        }
    }

    // quit the bot
    if (preg_match("/go to bed ".preg_quote($nick." ".$botpw)."/i", $message)) {
        quit($quit_message);
        fclose($ircsocket);
        $timestamp = "";
        uptime($timestamp, $uptime_data);
        
        $log->write(Logger::LOGMSG_INFO, "Bot down.");
        exit(0);
    }
}

//If you're out here an unhandled error probably occured.
?>
