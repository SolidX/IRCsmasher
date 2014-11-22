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

    class Basic extends BaseBotModule
    {

        public function __construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword)
        {
            parent::__construct($socket, $ircserver, $portNumber, $myNick, $channels, $realName, $botPword);
            $this->module_version = "1.0";
        }
        
        function runModule ($output, $com1, $com2, $com3, $name, $begin, $chan, $command, $message)
        {
            //if you say schnarchnase
            if (preg_match("/schnarchnase/", $message)) {
                delay_priv_msg($channel, "schnarchnasen sind cool!", 2);
            }

            //and now the masters ;)
            if (preg_match("/whois\smaniacbrain/", $message)) {
                priv_msg($chan, "maniacbrain is one of my masters and creators");
            }
            if (preg_match("/whois\srtf/", $message)) {
                priv_msg($chan, "rtf is one of my masters and creators");
            }
            if (preg_match("/whois\sircmasher/", $message)) {
                priv_msg($chan, "ircmasher is the greatest masher in the whole irc ;)");
            }

            //whatis words
            if (preg_match("/whatis\s[Rr][Tt][Ff][Mm]/", $message)) {
                priv_msg($chan, "rtfm = Read the Fuckin Manual!!!");
                return;
            }
            if (preg_match("/whatis\s[Ss][Tt][Ff][Ww]/", $message)) {
                priv_msg($chan, "stfw = Search the Fuckin Web!!!");
                return;
            }

            //entries of the old linux.php
            if (preg_match("/whatis\s[Ll]inux/", $message)) {
                priv_msg($chan, "Linux is a free Unix-type operating system originally created by Linus Torvalds with the assistance of developers around the world... and it's the best operating systems in the world :-)");
            }
            if (preg_match("/whatis\s[Gg][Nn][Uu]/", $message)) {
                priv_msg($chan, "GNU is a recursive acronym for \"GNU's Not Unix\"; it is pronounced \"guh-noo\".");
            }
            if (preg_match("/paragraph/", $message)) {
                priv_msg($chan, "§1. Ich habe immer Recht! §2. Sollte ich einmal nicht Recht haben so tritt automatisch §1 in Kraft...");
            }

            //!time/!date actions
            if (preg_match("/!time\b/i", $message)) {
                $time = date("g:i:s A");
                delay_priv_msg($chan, "$time", 2);
                return;
            }
            if (preg_match("/!date\b/i", $message)) {
                $date = date("D. M j, Y g:i:s A T");
                delay_priv_msg($chan, "$date", 2);
                return;
            }

            //!uptime action
            if (preg_match("/!uptime\b/i", $message)) {
                $uptime_data = "modules/data/uptime.data";
                $handle = fopen($uptime_data, "r");
                $timestamp_old = fread($handle, filesize($uptime_data));
                fclose($handle);
                $timestamp_new = time();
                $timestamp_diff = $timestamp_new - $timestamp_old;

                $dd = $timestamp_diff / (24*60*60);
                list ($dd_vk, $dd_nk) = explode('.', $dd);
                $dd = $dd_vk;

                $hh1 = "0." . $dd_nk;
                $hh2 = $hh1 * 24;
                list ($hh_vk, $hh_nk) = explode('.', $hh2);
                $hh = $hh_vk;

                $mm1 = "0." . $hh_nk;
                $mm2 = $mm1 * 60;
                list ($mm_vk, $mm_nk) = explode('.', $mm2);
                $mm = $mm_vk;

                $ss1 = "0." . $mm_nk;
                $ss2 = $ss1 * 60;
                list ($ss_vk, $ss_nk) = explode('.', $ss2);
                $ss = $ss_vk;

                $uptime_is = "has been up for " . $dd . " days, " . $hh
                . " hours, " . $mm . " minutes and " . $ss . " seconds";
                action_msg($chan, $uptime_is);
                return;
            }
        }
        
        public function getTriggers($user)
        {
            $user = parent::parseName($user);

            notice_msg($user, "Get Current Time: !time");
            notice_msg($user, "Get Current Date: !date");
            notice_msg($user, "Get Bot's Uptime: !uptime");
        }
    }
?>
