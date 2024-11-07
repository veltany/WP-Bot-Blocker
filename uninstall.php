<?php
// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Delete plugin options
delete_option('wp_bot_blocker_recaptcha_site_key');
delete_option('wp_bot_blocker_recaptcha_secret_key');
delete_option('wp_bot_blocker_honeypot_api_key');
delete_option('wp_bot_blocker_excluded_bots');
delete_option('wp_bot_blocker_score_threshold');
delete_option('wp_bot_blocker_enable_honeypot');
delete_option('wp_bot_blocker_monitor_crawlers');
delete_option('wp_bot_blocker_monitor_scrapers');
delete_option('wp_bot_blocker_monitor_spammers');
delete_option('wp_bot_blocker_rate_limit_threshold');
delete_option('wp_bot_blocker_rate_limit_window');
delete_option('wp_bot_blocker_rate_limit_block_duration');
delete_option('wp_bot_blocker_enable_traffic_monitor');
delete_option('wp_bot_blocker_block_bg_color');
delete_option('wp_bot_blocker_block_font_color');
delete_option('wp_bot_blocker_bot_detection_message');
delete_option('wp_bot_blocker_rate_limit_message');
delete_option('wp_bot_blocker_enable_recaptcha_block');
delete_option('wp_bot_blocker_log_retention');

// Drop custom database tables
$logs_table = $wpdb->prefix . 'wp_bot_blocker_logs';
$traffic_table = $wpdb->prefix . 'wp_bot_traffic_logs';

$wpdb->query("DROP TABLE IF EXISTS $logs_table");
$wpdb->query("DROP TABLE IF EXISTS $traffic_table");

// Clear scheduled event for log cleanup
wp_clear_scheduled_hook('wp_bot_blocker_cleanup_logs');
