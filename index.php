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
require_once('inc/ConfigManager.php');
require_once('inc/functions.php');
require_once('logger/SimpleLogger.php');
require_once('logger/HTMLLogger.php');

set_time_limit(0); //set unlimited maximum execution time

//Load configuration information
$configuration = ConfigManager::getInstance();

//Start up logger
if ($configuration->get_setting(ConfigManager::DEBUG_OUTPUT_HTML) === "1")
    $log = new HTMLLogger();
else
    $log = new SimpleLogger();

if ($configuration->get_setting(ConfigManager::DEBUG_MODE) === "1")
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
                $module = new $file($ircsocket, $configuration, $log); 
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
$errno = 0;
$errstr = "";
$ircsocket = fsockopen($configuration->get_setting(ConfigManager::IRC_SRV), $configuration->get_setting(ConfigManager::IRC_SRV_PORT), $errno, $errstr, 5);

if (!$ircsocket) {
    $log->write(Logger::LOGMSG_ERROR, "Error connecting to IRC server host.");
    $log->write(Logger::LOGMSG_ERROR, "Error Code {$errno}");
    $log->write(Logger::LOGMSG_ERROR, "Error Message {$errstr}");
    exit(1);
}

// login
if ($configuration->get_setting(ConfigManager::IRC_SRV_PASSWD) != "")
    authenticate($configuration->get_setting(ConfigManager::IRC_SRV));
register_connection($configuration->get_setting(ConfigManager::BOT_NICK), $configuration->get_setting(ConfigManager::BOT_REALNAME));

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
        register_connection($configuration->get_setting("bot_nick_alternate"), $configuration->get_setting(ConfigManager::BOT_REALNAME));
        continue;
    }

    // if end of MOTD
    if ($cmd_com[1] == "376")
    {
        // identify
        if ($configuration->get_setting(ConfigManager::BOT_IDENT_PASSWD) != "")
            identify($configuration->get_setting(ConfigManager::IRC_SRV_NICKSERV), $configuration->get_setting(ConfigManager::BOT_IDENT_PASSWD));

        // join default channels
        foreach ($configuration->get_setting(ConfigManager::BOT_CHANNELS) as $ch)
            join_channel($ch);
        
        continue;
    }

    // commands after connection is established
    // - on join say hello - 
    if ($cmd_com[1] == "332") {
        delay_priv_msg($cmd_com[3], $configuration->get_setting("bot_greetings"), 5);
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
    $list_triggers = preg_match("/!triggers/i", $message);
    foreach ($modules as $exec_module) {
        if ($list_triggers)
            $exec_module->getTriggers($sender_nick);
        else
            $exec_module->runModule($incoming, $com1, $com2, $com3, $sender_nick, $cmd_com, $chan, $command, $message);
    }

    // On being kicked
    if ($cmd_com[1] == "KICK") {
        //Remove channel from currently active channels
        $current_channels = $configuration->get_setting(ConfigManager::BOT_CHANNELS);
        if(($key = array_search($cmd_com[2], $current_channels)) !== false) {
            unset($current_channels[$key]);
        }
        
        if ($configuration->get_setting("auto_rejoin") === "1")
            join_channel($cmd_com[2]);
    }

    // quit the bot
    if (preg_match("/go to bed ".preg_quote($configuration->get_setting(ConfigManager::BOT_NICK)." ".$configuration->get_setting(ConfigManager::BOT_ADMIN_PASSWD))."/i", $message)) {
        quit($configuration->get_setting("bot_quit_message"));
        fclose($ircsocket);
        $timestamp = "";
        uptime($timestamp, $uptime_data);
        
        $log->write(Logger::LOGMSG_INFO, "Bot down.");
        exit(0);
    }
}

//If you're out here an unhandled error probably occured.
?>
