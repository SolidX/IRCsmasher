<?php
    /**************************************************
     * Nico Nico Douga（ニコニコ動画）Link Preview Module
     * IRCsmasher - based on IRCmasher by Ralf Oechsner and Volker Latainski
     *
     * @Project: IRCsmasher
     * @License: GNU General Public License v2
     *
     **************************************************/

    require_once("BaseBotModule.php");

    class NNDLinkPreview extends BaseBotModule
    {
        public function __construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword)
        {
            parent::__construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword);
            $this->module_version = "1.0";
        }
        
        public function runModule($output, $com1, $com2, $com3, $com4, $name, $begin, $chan, $command, $message)
        {
            if (preg_match("/nicovideo\.jp\/watch\/(sm|nm)/i", $output))
            {
                $startPt = stripos($output, "watch/") + 6;
                $code = substr($output, $startPt);
                
                while(!ctype_alnum($code))
                    $code = substr($code, 0, strlen($code) - 1);

                if (preg_match("/#[A-Za-z0-9_\-~#]+/i", $chan))
                    $this->getVidData($code, $chan);
                else
                {
                    $name = parent::parseName($name);
                    $this->getVidData($code, $name);
                }
                return;
            }
        }
        
        private function getVidData($vidCode, $channel)
        {
            $vidCode = trim($vidCode);

            $api = fopen("http://ext.nicovideo.jp/api/getthumbinfo/$vidCode","r");
            $vidData = "";

            while ($data = fread($api, 4096))
                $vidData .= $data;

            fclose($api);

            if (preg_match('/nicovideo_thumb_response status="fail"/i', $vidData))
            {
                priv_msg($channel, parent::BOLD.$vidCode.parent::BOLD." has been deleted or is no longer availible.");
                echo "<b>Nico Video Lookup:</b> Video Code: $vidCode\tStatus: Fail<br />\n";
            }
            else
            {
                $xml = simplexml_load_string($vidData);
                $title = $xml->thumb[0]->title;
                $length = $xml->thumb[0]->length;
                $views = $xml->thumb[0]->view_counter;

                priv_msg($channel, parent::BOLD."Title: ".parent::BOLD.$title);
                priv_msg($channel, parent::BOLD."Length: ".parent::BOLD.$length."     ".parent::BOLD."Views: ".parent::BOLD.$views);
                echo "<b>Nico Video Lookup:</b> Title: $title\tLength: $length\tViews: $views<br />\n";
            }
        }
        
        public function getTriggers($user)
        {
            return;
        }
    }
?>
