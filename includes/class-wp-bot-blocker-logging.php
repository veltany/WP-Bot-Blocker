<?php
class WP_Bot_Blocker_Logging {

    public static function create_log_table() {
        
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_bot_blocker_logs';

    $charset_collate = $wpdb->get_charset_collate();
    
 
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        ip_address VARCHAR(100) NOT NULL,
        user_agent TEXT NOT NULL,
        reason VARCHAR(50) DEFAULT 'General' NOT NULL,
        blocked_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


    public static function log_attempt($ip_address, $reason, $user_agent) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_bot_blocker_logs';

        $wpdb->insert($table_name, array(
            'ip_address' => sanitize_text_field($ip_address),
            'user_agent' => sanitize_text_field($user_agent),
            'blocked_time' => current_time('mysql'), 
            'reason' => sanitize_text_field($reason)
        ));
    }
   public static function schedule_log_cleanup() {
    if (!wp_next_scheduled('wp_bot_blocker_cleanup_logs')) {
        wp_schedule_event(time(), 'daily', 'wp_bot_blocker_cleanup_logs');
    }
}

public static function clear_old_logs() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_bot_blocker_logs';
    $retention = (int) get_option('wp_bot_blocker_log_retention', 3); // Default to 3 months

    $wpdb->query($wpdb->prepare(
        "DELETE FROM $table_name WHERE blocked_time < NOW() - INTERVAL %d MONTH",
        $retention
    ));
}

public static function clear_all_logs() {
    global $wpdb;
    $logs_table = $wpdb->prefix . 'wp_bot_blocker_logs';
    $traffic_table = $wpdb->prefix . 'wp_bot_traffic_logs';

    $wpdb->query("TRUNCATE TABLE $logs_table");
    $wpdb->query("TRUNCATE TABLE $traffic_table");
}

}

    
