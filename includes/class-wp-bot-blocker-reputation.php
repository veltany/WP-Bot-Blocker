<?php
if (!defined('ABSPATH')) exit;

class WP_Bot_Blocker_Reputation {
    private static $api_url = 'https://ip-api-example.com/check/';

    public static function check_blacklist($ip_address) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_bot_blacklist';
        $result = $wpdb->get_var($wpdb->prepare("SELECT ip_address FROM $table_name WHERE ip_address = %s", $ip_address));
        
        if ($result) return true;

        // Alternatively, use an external API
        $response = wp_remote_get(self::$api_url . urlencode($ip_address));
        if (is_wp_error($response)) return false;

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return isset($data['blacklisted']) && $data['blacklisted'] === true;
    }
}
