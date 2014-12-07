<?php
    /**************************************************
     *
     * IRCsmasher - based on IRCmasher by Ralf Oechsner and Volker Latainski
     *
     * @Project: IRCsmasher
     * @License: GNU General Public License v2
     *
     **************************************************/

    // Function to send the commands to the server
    function write_socket($param) {
        global $ircsocket;
        fputs($ircsocket, "$param\n\r");
    }
    
    // function to send an ACTION message
    function action_msg($target, $text) {
        write_socket("PRIVMSG ".$target." :\001ACTION $text\001");
    }

    // function to send a NOTICE message
    function notice_msg($target, $text) {
        write_socket("NOTICE ".$target." :$text");
    }

    // function to send a private message to nick
    function priv_msg($target, $text, $lag = false) {
        if ($lag)
            time_lag($text);
        write_socket("PRIVMSG {$target} :{$text}");
    }

    // function to send a private message with low lag
    function delay_priv_msg($target, $text, $delay) {
        sleep($delay);
        write_socket("PRIVMSG {$target} :{$text}");
        return 0;
    }

    function write_action($action) {
        global $ircsocket;
        sleep(2);
        fputs($ircsocket, $action . "\n\r");
    }

    //make it look more realistic
    function time_lag($text) {
        $delay = (strlen($text)) / ($configuration->get_setting("strokes") / 60);
        sleep($delay);
    }

    //create the timestamp for uptime
    function uptime($timestamp, $uptime_data) {
        $handle = fopen($uptime_data, "w");
        fwrite($handle, $timestamp);
    }
    
    /**
     * This function is used to set a 'connection password'.
     * If the IRC server has a password, it must be set before any attempt to register the connection is made.
     * 
     * @param string $passwd The IRC server password.
     */
    function authenticate($passwd) {
        if ($passwd != "")
            write_socket("PASS {$passwd}"); 
    }
    
     /**
     * Changes user's nickname to the specified nick.
     * 
     * @param string $new_nick The desired username / nickname to use
     */
    function change_nick($new_nick) {      
        write_socket("NICK {$new_nick}");
        ConfigManager::getInstance()->set_setting(ConfigManager::BOT_NICK, $new_nick);
        //TODO: Handle nick name already in use
    }
    
    /**
     * This function registers a new connection with an IRC server.
     * 
     * @param string $nick The desired username / nickname to use
     * @param string $real_name The "real name" to use
     * @param string $hostname (Optional) Host name. Do not specify unless you know what you are doing.
     * @param string $servername (Optional) Server name. Do not specify unless you know what you are doing.
     */
    function register_connection($nick, $real_name, $hostname = "0", $servername = "0") {
        change_nick($nick);
        write_socket("USER {$nick} {$hostname} {$servername} :{$real_name}");
        sleep(1);
    }
    
    /**
     * Authenticates that a user with a given nick is who they say they are.
     * 
     * @param string $nickserv Name of the NickServ (a bot in charge of authenticating users).
     * @param string $passwd The password used to identify a given nick.
     */
    function identify($nickserv, $passwd) {
        priv_msg($nickserv, "IDENTIFY {$passwd}");
    }
    
    /**
     * Sends a JOIN command to the IRC server in order to join the specified channel.
     * 
     * @param string $channel A single channel to join. eg: #ircsmasher
     * @param string $key A password to join the channel. Do not specify if not required.
     */
    function join_channel($channel, $key = null) {
        //TODO: Add some sort of validation to ensure channel is just a single one.
        $channel = trim($channel);
        
        if ($key === null)
            write_socket("JOIN $channel");
        else
            write_socket("JOIN $channel $key");
        
        $current_channels = ConfigManager::getInstance()->get_setting(ConfigManager::BOT_CHANNELS);
        array_push($current_channels, $channel);
        ConfigManager::getInstance()->set_setting(ConfigManager::BOT_CHANNELS, array_unique($current_channels));
        
        sleep(1);
    }
    
    /**
     * Terminates a client session.
     * 
     * @param string $quit_message (Optional) Message to display on quitting.
     */
    function quit($quit_message = "") {
        write_socket("QUIT : {$quit_message}");
    }
    
    /**
     * Reply to a ping from the specified target.
     * Do not use this if you have no idea what you are doing.
     * 
     * @param string $target The daemon which sent a PING request and optionally the daemon to forward the reply to.
     */
    function pong($target) {
        write_socket("PONG {$target}");
    }
    
    /**
     * Requests a list of visible users currently in the specified channel(s).
     * If no channel is provided, you will receive a list of all visbile channels and users.
     * 
     * @param string $channel (Optional) A single channel or a comma separated list of channels.
     */
    function names($channel = "") {
         write_socket(trim("NAMES {$channel}"));
    }
    
    /**
     * Kicks a user out of a channel given the bot has the necessary permisison to do so.
     * 
     * @param string $channel The channel you wish to kick a user from.
     * @param string $user The nick of the user to kick.
     * @param string $reason (Optional) Message to display as the reason for kicking the user.
     */
    function kick($channel, $user, $reason = "") {
        write_socket(trim("KICK {$channel} {$user} {$reason}"));
    }
?>
