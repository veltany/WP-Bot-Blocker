<?php
if (!defined('ABSPATH')) exit;

class WP_Bot_Blocker_Admin {
  
  private $helper;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_recaptcha_settings'));
        add_action('admin_init', array($this, 'handle_manual_cleanup'));
          
          $this->helper = new WP_Bot_Blocker_Helper(); 


    }

    public function add_admin_menu() {
        add_menu_page(
            __('Bot Blocker', 'wp-bot-blocker'),
            __('Bot Blocker', 'wp-bot-blocker'),
            'manage_options',
            'wp-bot-blocker',
            array($this, 'display_logs_page'),
            'dashicons-shield',
            6
        );

        add_submenu_page(
            'wp-bot-blocker',
            __('Bot Blocker Settings', 'wp-bot-blocker'),
            __('Settings', 'wp-bot-blocker'),
            'manage_options',
            'wp-bot-blocker-settings',
            array($this, 'display_settings_page')
        );
        add_submenu_page(
        'wp-bot-blocker',
        __('Live Traffic Monitor', 'wp-bot-blocker'),
        __('Live Traffic Monitor', 'wp-bot-blocker'),
        'manage_options',
        'wp-bot-blocker-traffic',
        array($this, 'display_traffic_monitor_page')
    );
    }

public function register_recaptcha_settings() {
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_recaptcha_site_key');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_recaptcha_secret_key');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_honeypot_api_key');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_excluded_bots');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_score_threshold');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_enable_honeypot');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_monitor_crawlers');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_monitor_scrapers');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_monitor_spammers');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_block_bg_color');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_block_font_color');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_block_message');
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_enable_recaptcha_block');
    
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_enable_traffic_monitor'); 
    register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_rate_limit_threshold'); // Request threshold
register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_rate_limit_window'); // Time window
register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_rate_limit_block_duration'); // Block duration
register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_rate_limit_message'); // Custom rate limit message
register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_bot_detection_message'); // Custom bot detection message
  register_setting('wp-bot-blocker-settings', 'wp_bot_blocker_log_retention');


}

public function display_settings_page() {
   
   settings_errors();

    include WP_BOT_BLOCKER_DIR. 'admin/views/settings-page.php';


}


    public static function create_blacklist_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_bot_blacklist';

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            ip_address varchar(100) NOT NULL,
            reason text NOT NULL,
            added_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY ip_address (ip_address)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

 public function display_logs_page() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'wp_bot_blocker_logs';

      $cache_key = $table_name;
      $logs = $this->helper->cache_getresult("SELECT * FROM $table_name ORDER BY blocked_time DESC LIMIT 20", $cache_key);
        

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Bot Blocker Logs', 'wp-bot-blocker') . '</h1>';
        echo '<table class="widefat">';
        echo '<thead><tr><th><b>ID</b></th><th><b>' . esc_html__('IP Address', 'wp-bot-blocker') . '</b></th>
        <th><b>' . esc_html__('Reason', 'wp-bot-blocker') . '</b></th>
        <th>' . esc_html__('User-Agent', 'wp-bot-blocker') . '</th><th>' . esc_html__('Blocked Time', 'wp-bot-blocker') . '</th></tr></thead>';
        echo '<tbody>';
        foreach ($logs as $log) {
            echo "<tr><td>" . esc_html($log->id) . "</td><td>" . esc_html($log->ip_address) . "</td>
           <td>" . esc_html($log->reason) . "</td>
            <td>" . esc_html($log->user_agent) . "</td><td>" . esc_html($log->blocked_time) . "</td></tr>";
        }
        echo '</tbody></table></div>';
    }

public function display_traffic_monitor_page() {
    global $wpdb ;
    $rate_limited_logs = $this->helper->cache_getresult("SELECT * FROM {$wpdb->prefix}wp_bot_blocker_logs WHERE reason = 'rate_limit' ORDER BY blocked_time DESC LIMIT 50",      "traffic_rate_limit_logs");
   
    $all_logs = $this->helper->cache_getresult("SELECT * FROM {$wpdb->prefix}wp_bot_traffic_logs ORDER BY visit_time DESC LIMIT 50", "all_logs_traffic_monitor");

    include WP_BOT_BLOCKER_DIR. 'admin/views/traffic-monitor-page.php';
}


public static function create_traffic_logs_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_bot_traffic_logs';

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        ip_address VARCHAR(100) NOT NULL,
        user_agent TEXT NOT NULL,
        page_visited TEXT NOT NULL,
        visit_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
public function handle_manual_cleanup() {
    if (isset($_POST['wp_bot_blocker_clear_logs']) && check_admin_referer('wp_bot_blocker_clear_logs')) {
        WP_Bot_Blocker_Logging::clear_all_logs();
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('All logs have been cleared.', 'wp-bot-blocker') . '</p></div>';
        });
    }
}

}

