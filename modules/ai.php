<?php
    /**************************************************
     *
     * IRCsmasher - based on IRCmasher by Ralf Oechsner and Volker Latainski
     *
     * @Project: IRCsmasher
     * @License: GNU General Public License v2
     *
     **************************************************/

    require_once("BaseBotModule.php");

    class AI extends BaseBotModule
    {
        /** @var boolean */
        private $iq_status;
        /** @var boolean */
        private $datapath;
        /** @var boolean */
        private $module_src;
        /** @var array */
        private $db;

        public function __construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword)
        {
            parent::__construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword);
            $this->module_version = "1.0";

            $this->iq_status = true;        
            $this->module_name = "ai";
            $this->datapath = "modules/data/";
            $this->module_src = $this->datapath . $$this->module_name . ".data";
            $this->db = file($$this->module_src);
        }

        private function iq_function($name, $begin, $chan, $command, $message) {
            for ($x = 0; $x < count($this->db); $x++) {
                list ($a, $b) = split('[|]', $this->db[$x]);
                if (preg_match("/" . preg_quote($a) . "/i", $message) && $command == "PRIVMSG") {
                    if (substr($b, 0, 7) == "/ACTION") {
                        $action = str_replace("/ACTION ", "", $b); //don't put '/ACTION' out                                             
                        $action = rtrim($action);  //delete useless signs on the right                                                 
                        priv_msg($chan, "\001ACTION " . $action . " \001");
                    } else {
                        priv_msg($chan, "$b");
                    }
                }
            }
        }

        public function runModule($output, $com1, $com2, $com3, $com4, $name, $begin, $chan, $command, $message)
        {
            $arguments = explode(" ",$message);  //split message into words
            
            /*if ($arguments[0] == "refresh_db" && $arguments[1] == $this->botpw && $command == "PRIVMSG")
            {
                fclose($db);  //close db
                unset($db);
                $this->db = file($this->module_src);  //and read it again
            }*/

            if ($arguments[0] == '!mute' && $arguments[1] == $this->botpw && $command == "PRIVMSG")
            {
                $this->iq_status = false;
                priv_msg($chan, "I'll be quiet now.");
            }

            if ($arguments[0] == '!unmute' && $arguments[1] == $this->botpw && $command == "PRIVMSG")
                $this->iq_status = true;

            if (!isset($this->iq_status) || $this->iq_status)
                $this->iq_function($name, $begin, $chan, $command, $message);
        }

        public function getTriggers($user)
        {
            notice_msg(parent::parseName($user), "Silence bot AI: !mute");
            notice_msg(parent::parseName($user), "Un-mute bot AI: !unmute");
            //TODO: Add messages for remaining Triggers
            return;
        }
    }
?>
