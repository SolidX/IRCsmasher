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

        public function __construct($socket, ConfigManager $config, Logger $log)
        {
            parent::__construct($socket, $config, $log);
            $this->module_version = "1.0.0";

            $this->iq_status = true;        
            $this->module_name = "ai";
            $this->datapath = "modules/data/";
            $this->module_src = $this->datapath . $this->module_name . ".data";
            $this->db = file($this->module_src);
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

        public function runModule($output, $com1, $com2, $com3, $name, $begin, $chan, $command, $message)
        {
            if ($command != "PRIVMSG")
                return; //Ensure these commands can only be triggered by messages from users
            
            $target = $this->determineReplyTarget($chan, $name);
            $arguments = explode(" ",$message);  //split message into words
            $bot_passwd = $this->configuration->get_setting(ConfigManager::BOT_ADMIN_PASSWD);
            
            /*if ($arguments[0] == "refresh_db" && $arguments[1] == $bot_passwd)
            {
                fclose($db);  //close db
                unset($db);
                $this->db = file($this->module_src);  //and read it again
            }*/

            if ($arguments[0] == '!mute' && $arguments[1] == $bot_passwd)
            {
                $this->iq_status = false;
                priv_msg($target, "I'll be quiet now.");
            }

            if ($arguments[0] == '!unmute' && $arguments[1] == $bot_passwd)
            {
                $this->iq_status = true;
                priv_msg($target, "Yay!");
            }

            if (!isset($this->iq_status) || $this->iq_status)
                $this->iq_function($name, $begin, $target, $command, $message);
        }

        /**
         * Enumerates any triggers this module may contain to a requesting user.
         * 
         * @param string $target Nick of the user to respond to
         */
        public function getTriggers($target)
        {
            notice_msg($target, "Silence bot AI: !mute");
            notice_msg($target, "Un-mute bot AI: !unmute");
            return;
        }
    }
?>
