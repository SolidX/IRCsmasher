<?php
    /**************************************************
     *
     * IRCsmasher - based on IRCmasher by Ralf Oechsner and Volker Latainski
     *
     * @Project: IRCsmasher
     * @License: GNU General Public License v2
     *
     **************************************************/

    abstract class Logger
    {
        /** @var int Informational type of message */
        const LOGMSG_INFO = 1;
        /** @var int Warning type of message */
        const LOGMSG_WARNING = 2;
        /** @var int Error type of message */
        const LOGMSG_ERROR = 4;
        /** @var int Unknown type of message */
        const LOGMSG_UNKNOWN = 0;
        
        /** @var bool False by default */
        protected $logging_enabled = false;
        
        /**
         * Outputs an entry to a logging medium.
         * eg. Writing a line to the console or a row to a database
         * 
         * @param string $message
         */
        abstract public function write($msg_type = LOGMSG_UNKNOWN, $message = null);
        
        /**
         * Enables writing log messages;
         */
        public function enable_logging()
        {
            $this->logging_enabled = true;
        }
        
        /**
         * Disables writing of log messages
         */
        public function disable_logging()
        {
            $this->logging_enabled = false;        
        }
        
        /**
         * Returns true if logging is enabled, false otherwise
         * 
         * @return bool
         */
        public function is_logging_enabled()
        {
            return $this->logging_enabled === true;
        }
    }
?>