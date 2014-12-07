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

    class Admin extends BaseBotModule
    {
        /**
         * Creates a new instance of the Admin module.
         * 
         * @param resource $socket The resource representing an open connection to an IRC server.
         * @param ConfigManager $config The ConfigManager which has all the current bot settings.
         * @param Logger $log Log to write any information to.
         */
        public function __construct($socket, ConfigManager $config, Logger $log)
        {
            parent::__construct($socket, $config, $log);
            $this->module_version = "1.0.0";
        }
        
        function runModule ($output, $com1, $com2, $com3, $name, $begin, $chan, $command, $message)
        {
            if ($command != "PRIVMSG")
                return; //Ensure these commands can only be triggered by messages from users
            
            //split message into single words
            $arguments = explode(" ", $message);
            $bot_passwd = $this->configuration->get_setting(ConfigManager::BOT_ADMIN_PASSWD);

            if ($arguments[0] == "giveop" && $arguments[1] == $bot_passwd) {
                write_socket("MODE $arguments[2] +o $arguments[3]");
            }

            if ($arguments[0] == "takeop" && $arguments[1] == $bot_passwd) {
                write_socket("MODE $arguments[2] -o $arguments[3]");
            }

            if ($arguments[0] == "voice" && $arguments[1] == $bot_passwd) {
                write_socket("MODE $arguments[2] +v $arguments[3]");
            }

            if ($arguments[0] == "dvoice" && $arguments[1] == $bot_passwd) {
                write_socket("MODE $arguments[2] -v $arguments[3]");
            }

            if ($arguments[0] == "kick" && $arguments[1] == $bot_passwd) {
                kick($arguments[2], $arguments[3]);
            }

            if ($arguments[0] == "identify" && $arguments[1] == $bot_passwd) {
                change_nick($nick);
                identify($nickserv, $ident);
            }
            
            if ($arguments[0] == "join" && $arguments[1] == $bot_passwd) {
                join_channel($arguments[2]); //TODO: Take password protected channels in to account.
                $this->log->write(Logger::LOGMSG_INFO, "JOINing {$arguments[2]}");
            }
            
            if ($arguments[0] == "nick" && $arguments[1] == $bot_passwd) {
                change_nick($arguments[2]);
            }

            //let the bot talk for you :-)
            if (preg_match("/say [A-Za-z0-9_\-#]+ ".$bot_passwd."/i", $output))
            {
                $message = substr($output, stripos($output, ":", 1) + 1);
                $startPt = stripos($message, "say");
                $message = substr($message, 0, $startPt) . substr($message, $startPt + 3);
                $message = str_ireplace("say", "", $message, $limit);
                $message = str_ireplace($bot_passwd, "", $message);
                $message = str_ireplace($arguments[1], "", $message);
                $message = trim($message);

                $this->log->write(Logger::LOGMSG_INFO, "Say \"{$message}\" to {$arguments[1]}");

                if ($arguments[1] == "all")
                {
                    foreach($this->configuration->get_setting(ConfigManager::BOT_CHANNELS) as $target)
                    {
                        priv_msg($target, $message);
                    }
                }
                else
                    priv_msg($arguments[1], $message);
                
                return;
            }
            
            #if ($arguments[0] == "restart" && $arguments[1] == $botpw) {
                //TODO: Implement bot restart feature
            #}
        }
        
        /**
         * Enumerates any triggers this module may contain to a requesting user.
         * 
         * @param string $target Nick of the usre to respond to
         */
        public function getTriggers($target)
        {
            //TODO: Implement getTriggers
            return;
        }
    }
?>
