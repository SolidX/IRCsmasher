<?php
    /**************************************************
     *
     * IRCsmasher - based on IRCmasher by Ralf Oechsner and Volker Latainski
     *
     * @Project: IRCsmasher
     * @License: GNU General Public License v2
     *
     **************************************************/

    require_once("Logger.php");
    
    class SimpleLogger extends Logger
    {
        public function write($msg_type = LOGMSG_UNKNOWN, $message = null)
        {
            if (!$this->logging_enabled || $message === null) return;
            
            //TODO: Maybe implement CLI color output
            echo trim($message, "\n\r\0\x0B") . PHP_EOL;
        }
    }
?>