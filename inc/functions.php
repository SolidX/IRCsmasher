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
        write_socket("PRIVMSG ".$target." :".$text);
    }

    // function to send a private message with low lag
    function delay_priv_msg($target, $text, $delay) {
        sleep($delay);
        write_socket("PRIVMSG ".$target." :".$text);
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
?>
