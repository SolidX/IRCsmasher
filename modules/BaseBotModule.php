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
        /** @var string */
        protected $server;
        /** @var string */
        protected $port;
        /** @var string */
        protected $nick;
        /** @var string */
        protected $channel;
        /** @var string */
        protected $real_name;
        /** @var string */
        protected $botpw;
        /** @var string */
        protected $module_version;

        public function __construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword)
        {
            $this->module_version = "0.1";

            $this->ircsocket = $socket;
            $this->server = $ircserver;
            $this->port = $portNumber;
            $this->nick = $myNick;
            $this->channel = explode(";", $channels);
            $this->real_name = $realName;
            $this->botpw = $botPword;
        }
        
        /**
         * Cleans up a provided message's sender's nick.
         * 
         * @param string $name
         * @return string
         */
        public function parseName($name)
        {
            if (strchr($name, "!"))
                return substr(trim($name), 0, strpos(trim($name), "!"));
            return $name;
        }
        
        /**
         * Sends a notification to a user who uses the !triggers trigger explaining any triggers that are made available by this module.
         * @param string $user
         */
        abstract public function getTriggers($user);
        
        /**
         * Takes a message, parses it to see if it's activated this module's functionality and then executes the necessary functionality.
         * 
         * @param string $output
         * @param string $com1
         * @param string $com2
         * @param string $com3
         * @param string $name
         * @param string $begin
         * @param string $chan
         * @param string $command
         * @param string $message
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
