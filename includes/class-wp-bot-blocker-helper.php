<?php
if (!defined('ABSPATH')) exit;

class WP_Bot_Blocker_Helper {

    public  function log($message) {
        
            $log_file = plugin_dir_path(__FILE__) . '../logs/logs.txt';
            $time = gmdate('Y-m-d H:i:s');
            $log_entry = "[$time] $message \n" . PHP_EOL;

            // Ensure the log directory exists
            if (!file_exists(plugin_dir_path(__FILE__) . '../logs')) {
                mkdir(plugin_dir_path(__FILE__) . '../logs', 0755, true);
            }

            // Append log entry to file
            file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
        
    }


}

    
