<?php
    class ConfigManager
    {
        /** @var array Configuration information (may contain runtime changes) */
        private $conf;
        /** @var array Configuration information as originally loaded */
        private $original_conf;
        
        //Convenient consts to access common core settings
        const IRC_SRV = "irc_server";
        const IRC_SRV_PORT = "irc_server_port";
        const IRC_SRV_PASSWD = "irc_server_password";
        const IRC_SRV_NICKSERV = "irc_server_nickserv";
        const BOT_NICK = "bot_nick";
        const BOT_REALNAME = "bot_real_name";
        const BOT_ADMIN_PASSWD = "bot_admin_password";
        const BOT_IDENT_PASSWD = "bot_ident_password";
        const BOT_CHANNELS = "channel";
        const DEBUG_MODE = "debug_mode";
        const DEBUG_OUTPUT_HTML = "debug_output_html";

        
        /**
         * 
         * @staticvar ConfigManager $instance
         * @return ConfigManager
         */
        public static function getInstance() {
            static $instance = null;
            if ($instance === null)
                $instance = new static();
            
            return $instance;
        }
        
        /**
         * Loads config information from a .ini file and stores it in to memory.
         * NOTE: We ignore sections in the configuration .ini file.
         */
        protected function __construct() {
            $this->original_conf = parse_ini_file("config.ini", false);
            $this->conf = $this->original_conf;
            
            if (!$this->conf) {
                echo "Failed to load or parse configuration file."; //Logger doesn't exist yet, this is the only place we should directly echo.
                exit(3);
            }
        }
        
        /**
         * Retrieves the current value for a given configuration setting.
         * 
         * @param string $setting Name of the setting you'd like the value for
         * @return mixed Returns the value or null if the provided setting doesn't exist
         */
        public function get_setting($setting) {
            if (isset($this->conf[$setting]))
                return $this->conf[$setting];
            return null;
        }
        
        /**
         * Sets the value of a specific configuration setting.
         * 
         * @param string $setting The name of te setting you'd like to set a value for
         * @param mixed $value The new value of the specified setting
         */
        public function set_setting($setting, $value) {
            if ($setting !== null)
                $this->conf[$setting] = $value;
        }
        
        private function __clone() { /* NOPE */ }
        private function __wakeup()  { /* NOPE */ }
    }
?>