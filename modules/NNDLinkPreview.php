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
        public function __construct($socket, ConfigManager $config, Logger $log)
        {
            parent::__construct($socket, $config, $log);
            $this->module_version = "1.1.0";
        }
        
        public function runModule($output, $com1, $com2, $com3, $name, $begin, $chan, $command, $message)
        {
            $matches = array();
            if (preg_match("/nicovideo\.jp\/watch\/((sm|nm)[0-9]+)/i", $output, $matches))
            {
                $code = $matches[1];
                $target = $this->determineReplyTarget($chan, $name);
                $data = $this->getVideoData($code);
                
                if ($data)
                {
                    $xml = simplexml_load_string($data);                
                    if (strcasecmp($xml->attributes()->status, "fail") == 0)
                    {
                        priv_msg($target, "Information for " . parent::BOLD . $code . parent::BOLD . " could not be found.");
                        $this->log->write(Logger::LOGMSG_WARNING, "Nico Video Lookup: Video Code: {$code}\tStatus: Fail\tError Code: {$xml->error[0]->code}");
                    }
                    else
                    {
                        $title = $xml->thumb[0]->title;
                        $length = $xml->thumb[0]->length;
                        $views = $xml->thumb[0]->view_counter;

                        priv_msg($target, parent::BOLD . "Title: " . parent::BOLD . $title);
                        priv_msg($target, parent::BOLD . "Length: " . parent::BOLD . $length . "     " . parent::BOLD . "Views: " . parent::BOLD . $views);
                        $this->log->write(Logger::LOGMSG_INFO, "Nico Video Lookup: Title: {$title}\tLength: {$length}\tViews: {$views}");
                    }
                }
                else
                    $this->log->write(Logger::LOGMSG_WARNING, "Could not access Nico Nico API.");
            }
        }
        
        /**
         * Sends a request to the NND API to get information about a video.
         * 
         * @param string $video_code NND video id (starting in sm or nm)
         * @param string $target The user or channel to send the results to.
         * @return string Returns the raw XML results from the API as a string or null if a connection could not be established.
         */
        private function getVideoData($video_code)
        {
            $video_code = trim($video_code);

            $api = fopen("http://ext.nicovideo.jp/api/getthumbinfo/{$video_code}","r");
            
            if ($api)
            {
                $vid_info = "";

                while ($data = fread($api, 4096))
                    $vid_info .= $data;

                fclose($api);
                return $vid_info;
            }
            return null;
        }
        
        /**
         * Enumerates any triggers this module may contain to a requesting user.
         * 
         * @param string $target Nick of the user to respond to
         */
        public function getTriggers($target)
        {
            return;
        }
    }
?>
