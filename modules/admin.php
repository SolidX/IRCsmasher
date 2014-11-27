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
        public function __construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword)
        {
            parent::__construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword);
            $this->module_version = "1.0";
        }
        
        function runModule ($output, $com1, $com2, $com3, $name, $begin, $chan, $command, $message)
        {
            //split message into single words
            $arguments = explode(" ", $message);

            if ($arguments[0] == "giveop" && $arguments[1] == $this->botpw && $command == "PRIVMSG") {
                write_socket("MODE $arguments[2] +o $arguments[3]");
            }

            if ($arguments[0] == "takeop" && $arguments[1] == $this->botpw && $command == "PRIVMSG") {
                write_socket("MODE $arguments[2] -o $arguments[3]");
            }

            if ($arguments[0] == "voice" && $arguments[1] == $this->botpw && $command == "PRIVMSG") {
                write_socket("MODE $arguments[2] +v $arguments[3]");
            }

            if ($arguments[0] == "dvoice" && $arguments[1] == $this->botpw && $command == "PRIVMSG") {
                write_socket("MODE $arguments[2] -v $arguments[3]");
            }

            if ($arguments[0] == "kick" && $arguments[1] == $this->botpw && $command == "PRIVMSG") {
                kick($arguments[2], $arguments[3]);
            }

            if ($arguments[0] == "identify" && $arguments[1] == $this->botpw && $command == "PRIVMSG") {
                change_nick($nick);
                identify($nickserv, $ident);
            }
            
            if ($arguments[0] == "join" && $arguments[1] == $this->botpw && $command == "PRIVMSG") {
                join_channel($arguments[2]); //TODO: Take password protected channels in to account.
                echo "JOINing ".$arguments[2]."\n";
            }
            
            if ($arguments[0] == "nick" && $arguments[1] == $this->botpw && $command == "PRIVMSG") {
                change_nick($arguments[2]);
            }

            //let the bot talk for you :-)
            if (preg_match("/say [A-Za-z0-9_\-#]+ ".$this->botpw."/i", $output))
            {
                $message = substr($output, stripos($output, ":", 1) + 1);
                $startPt = stripos($message, "say");
                $message = substr($message, 0, $startPt) . substr($message, $startPt + 3);
                $message = str_ireplace("say", "", $message, $limit);
                $message = str_ireplace($this->botpw, "", $message);
                $message = str_ireplace($arguments[1], "", $message);
                $message = trim($message);

                echo "Say \"$message\" to ".$arguments[1];

                if ($arguments[1] == "all")
                {
                    foreach($this->channels as $target)
                    {
                        priv_msg($target, $message);
                    }
                }
                else
                    priv_msg($arguments[1], $message);
                
                return;
            }
            
            #  if ($arguments[0] == "restart" && $arguments[1] == $botpw && $command == "PRIVMSG") {
            #doesn't work yet
            #  }
        }
        
        public function getTriggers($user)
        {
            //TODO: Implement getTriggers
            return;
        }
    }
?>
