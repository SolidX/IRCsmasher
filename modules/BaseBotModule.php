<?php
    /**************************************************
     *
     * IRCsmasher - based on IRCmasher by Ralf Oechsner and Volker Latainski
     *
     * @Project: IRCsmasher
     * @License: GNU General Public License v2
     *
     **************************************************/

    abstract class BaseBotModule
    {
        const BOLD = "\x02";        //STX
        const ITALIC = "\x16";      //SYN
        const STRIKE = "\x12";      //DC2
        const UNDERLINE = "\x1F";   //US
        const COLOR = "\x03";       //ETX
	
        /** @var resource */
        protected $ircsocket;
        /** @var ConfigManager */
        protected $configuration;
        /** @var Logger */
        protected $log;
        /** @var string */
        protected $module_version;

        /**
         * Creates a new instance of this module.
         * 
         * @param resource $socket The resource representing an open connection to an IRC server.
         * @param ConfigManager $config The ConfigManager which has all the current bot settings.
         * @param Logger $log Log to write any information to.
         */
        public function __construct($socket, ConfigManager $config, Logger $log)
        {
            $this->module_version = "1.0.0";
            $this->configuration = $config;
            $this->log = $log;
        }
        
        /**
         * Sends a notification to a user who uses the !triggers trigger explaining any triggers that are made available by this module.
         * @param string $target
         */
        abstract public function getTriggers($target);
        
        /**
         * Takes a message, parses it to see if it's activated this module's functionality and then executes the necessary functionality.
         * 
         * @param string $output Raw incoming message from the IRC server
         * @param string $com1 Prefix component of the raw message (usually empty)
         * @param string $com2 Command component of the raw message (typically contains sender, status code / command & message target info)
         * @param string $com3 Params component of the raw message (typically contains a user's message)
         * @param string $name Name of the message sender (if available)
         * @param string[] $begin An array of the components constituting $com2
         * @param string $chan The target the message was sent to (either a channel or the bot's user name
         * @param string $command The command used to send the message (ex. PRIVMSG, NOTICE, ect.)
         * @param string $message The body of a user's message
         */
        abstract public function runModule($output, $com1, $com2, $com3, $name, $begin, $chan, $command, $message);
        
        /**
         * Fetches the current version of this module.
         * @return string
         */
        public function getVersion()
        {
            return $this->module_version;
        }

    }
?>
