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

    class Peak extends BaseBotModule
    {
        public function __construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword)
        {
            parent::__construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword);
            $this->module_version = "1.0";
        }
        
        private function peak_read($peak_datapath)
        {
            $peak_handle = fopen($peak_datapath, "r");
            $peak_data = fread($peak_handle, filesize($peak_datapath));
            $peak_data_split = explode("|",$peak_data);
            fclose($peak_handle);
            return $peak_data_split;
        }
        
        private function peak_write($count, $timestamp, $peak_datapath)
        {
            $new_peak_data = $count . "|" . $timestamp;
            $new_peak_handle = fopen($peak_datapath, "w");
            fwrite($new_peak_handle, $new_peak_data);
            fclose($new_peak_handle);
        }
        
        public function runModule($output, $com1, $com2, $com3, $name, $begin, $chan, $command, $message)
        {
            $str_namelist = explode(" ", $com3);
            $count = count($str_namelist) - 1;
            $namelist_code = explode(" ", $com2);
            $timestamp = time();

            if($namelist_code[1] == "353")
            {
                $peak_datafile = "peak_" . $namelist_code[4] . ".data";
                $peak_datapath = "modules/data/" . $peak_datafile;

                if(file_exists($peak_datapath) && filesize($peak_datapath) > "1")
                {
                    $new_peak_data_split = $this->peak_read($peak_datapath);

                    if($new_peak_data_split[0] < $count)
                        $this->peak_write($count, $timestamp, $peak_datapath);
                }
                else
                    $this->peak_write($count, $timestamp, $peak_datapath);
            }

            //on join => check for users
            $join_signs = explode(" ", $com2);
            if($join_signs[1] == "JOIN" && $name != $this->real_name)
            {
                names($com3);
                $peak_datafile = "peak_" . rtrim($com3) . ".data";
                $peak_datapath = "modules/data/" . $peak_datafile;

                if(file_exists($peak_datapath) && filesize($peak_datapath) > "1")
                {
                    $new_peak_data_split = $this->peak_read($peak_datapath);

                    if($new_peak_data_split[0] < $count)
                    {
                        $this->peak_write($count, $timestamp, $peak_datapath);
                    }
                }
                else
                   $this->peak_write($count, $timestamp, $peak_datapath);
            }

            //!peak
            if (preg_match("/!peak/i", $message))
            {
                $peak_datafile = "peak_" . $namelist_code[2] . ".data";
                $peak_datapath = "modules/data/" . $peak_datafile;

                $new_peak_data_split = $this->peak_read($peak_datapath);

                $peak_timestamp = date("D. M j, Y g:i:s A T", $new_peak_data_split[1]);
                $peak_msg = "The peak for $namelist_code[2] is $new_peak_data_split[0] users. It was reached on $peak_timestamp";
                delay_priv_msg($namelist_code[2], "$peak_msg", "6");
                return;
            }
        }
        
        public function getTriggers($user)
        {
            notice_msg(parent::parseName($user), "Get Channel Peak Users: !peak");
        }
    }
?>