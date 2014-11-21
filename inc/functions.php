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
        global $strokes;
        $delay = (strlen($text)) / ($strokes / 60);
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
     * @param string $nick The desired username / nickname to use
     */
    function change_nick($nick) {
        write_socket("NICK {$nick}");
        //TODO: Handle nick name already in use
        //TODO: Ensure that the bot is aware of it's own nick change (for any modules that depend on it).
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
        if ($key === null)
            write_socket("JOIN $channel");
        else
            write_socket("JOIN $channel $key");
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
?>
