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
    if ($enable_debugging)
    {
        if ($debug_html == "1")
            echo "Error connecting to host.</pre><body></html>";
        else
            echo "Error connecting to host.";
    }
    exit(1);
}

// login
if ($pass != "")
    authenticate($pass);
register_connection($nick, $real_name);

// main function
while (!feof($ircsocket)) {

    $incoming = fgets($ircsocket, 1024);

    // Split $incoming
    $output = explode(":", $incoming, 3);
    $com1 = $output[0]; //Prefix
    $com2 = $output[1]; //Command
    $com3 = $output[2]; //Params
    
    // Parse Command
    $cmd_com = explode(" ", $com2); //[0] - Originator, [1] Command / Status, [2+] (optional)
    
    // play PING PONG with the irc server
    if ($com1 == "PING ")
        pong($com2);

    // if username is in use
    if ($cmd_com[1] == "433")
        register_connection($nick_alternate, $real_name);

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
    }

    // commands after connection is established
    // - on join say hello - 
    // TODO: Fix this. Should say hi on channel join, instead says hi on recieving topic
    if ($cmd_com[1] == "332")
        delay_priv_msg($cmd_com[3], $hello, "5");
    
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
        
        if ($enable_debugging)
        {
            if ($debug_html == "1")
                echo "Bot down.</pre><body></html>";
            else
                echo "Bot down.";
        }
        
        exit(0);
    }

    if ($enable_debugging)
        echo $incoming;  // echos raw input from server
}

//If you're out here an unhandled error probably occured.
if ($enable_debugging && $debug_html == "1") {
    echo "</pre><body></html>";
}
?>
