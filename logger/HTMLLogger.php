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
    
    class HTMLLogger extends Logger
    {
        /**
         * Outputs opening HTML
         */
        public function __construct()
        {
            echo "<html><head><title>IRCsmasher Debug Mode</title></head><body style='background-color: black; color:text=\"\#ffffff\"'><pre>"  . PHP_EOL;
        }
        
        /**
         * Outputs log messages with HTML formatting.
         * Informational messages show up in cyan, warnings in yellow and errors in red.
         * 
         * @param int $msg_type Logger Message Type Constant
         * @param string $message
         */
        public function write($msg_type = LOGMSG_UNKNOWN, $message = null)
        {
            if (!$this->logging_enabled || $message === null) return;
            
            $message = trim($message, "\n\r\0\x0B");
            
            switch ($msg_type)
            {
                case Logger::LOGMSG_INFO:
                    echo "<span style='color: cyan'>{$message}</span>" . PHP_EOL;
                    break;
                case Logger::LOGMSG_WARNING:
                    echo "<span style='color: yellow'>{$message}</span>" . PHP_EOL;
                    break;
                case Logger::LOGMSG_ERROR:
                    echo "<span style='color: red'>{$message}</span>" . PHP_EOL;
                    break;
                case Logger::LOGMSG_UNKNOWN:
                default:
                    echo $message . PHP_EOL;
            }
        }
        
        /**
         * Closes opening HTML tags.
         */
        public function __destruct()
        {
            echo "</pre></body></html>";
        }
    }
?>