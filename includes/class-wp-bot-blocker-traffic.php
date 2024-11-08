<?php
if (!defined('ABSPATH')) exit;

class WP_Bot_Blocker_Traffic {

    public static function log_visit() {
        if (!get_option('wp_bot_blocker_enable_traffic_monitor', false)) {
            return; // Exit if monitoring is disabled
        }
        $bot_blocker_headers = new WP_Bot_Blocker_Headers();

        $ip_address = $bot_blocker_headers->get_ip();
        $user_agent = $bot_blocker_headers->get_user_agent();

        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_bot_traffic_logs';

        $ip_address = sanitize_text_field($ip_address ?? '');
        $user_agent = sanitize_text_field($user_agent ?? '');
        $page_visited = esc_url_raw($_SERVER['REQUEST_URI'] ?? '');

        $wpdb->insert($table_name, array(
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'page_visited' => $page_visited,
            'visit_time' => current_time('mysql')
        ), array('%s', '%s', '%s', '%s'));
    }
}
