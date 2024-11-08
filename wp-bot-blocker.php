<?php
/**
 * Plugin Name: WP Bot Blocker
 * Description: Advanced bot blocking with Google reCAPTCHA for enhanced verification.
 * Version: 2.2
 * Author: Samuel Chukwu 
 * License: GPL2
 * Text Domain: wp-bot-blocker
 * Author URI: https://yourwebsite.com
 * GitHub Plugin URI: https://github.com/veltany/wp-bot-blocker
 * GitHub Branch: main
 */

if (!defined('ABSPATH')) exit;

define('WP_BOT_BLOCKER_VERSION', '2.1');
define('WP_BOT_BLOCKER_DIR', plugin_dir_path(__FILE__));
define('WP_BOT_BLOCKER_URL', plugin_dir_url(__FILE__));

require WP_BOT_BLOCKER_DIR.'lib/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/veltany/wp-bot-blocker/',
	WP_BOT_BLOCKER_DIR.'wp-bot-blocker.php', //Full path to the main plugin file or functions.php.,
	'wp-bot-blocker'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//Optional: If you're using a private repository, specify the access token like this:
///$myUpdateChecker->setAuthentication('your-token-here');
 
/*$myUpdateChecker = PucFactory::buildUpdateChecker(
	WP_BOT_BLOCKER_URL.'update/wp-bot-blocker.json',
	WP_BOT_BLOCKER_DIR.'wp-bot-blocker.php', //Full path to the main plugin file or functions.php.
	'wp-bot-blocker'
);
*/

require_once WP_BOT_BLOCKER_DIR . 'admin/class-wp-bot-blocker-admin.php';
require_once WP_BOT_BLOCKER_DIR . 'includes/class-wp-bot-blocker-detection.php';
require_once WP_BOT_BLOCKER_DIR . 'includes/class-wp-bot-blocker-logging.php';
require_once WP_BOT_BLOCKER_DIR . 'includes/class-wp-bot-blocker-recaptcha.php';
require_once WP_BOT_BLOCKER_DIR . 'includes/class-wp-bot-blocker-reputation.php';
require_once WP_BOT_BLOCKER_DIR . 'includes/class-wp-bot-blocker-traffic.php';
require_once WP_BOT_BLOCKER_DIR. 'includes/class-abuseipdb-api.php';
include_once(plugin_dir_path(__FILE__) . 'includes/class-honeypot-api.php'); 
include_once(plugin_dir_path(__FILE__) . 'includes/class-recaptcha-v3-api.php'); 



// Main WPBotBlocker class
 class WPBotBlocker {
    private static $instance = null;
    
    public $detector ;
    public $rulecheck;
    public $helper ;
    public $honeypot;

    // Singleton pattern to ensure only one instance
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Constructor: Set up hooks and load dependencies
    private function __construct() {
        
       
        // Load dependencies
        $this->load_dependencies();
        
        $this->register_hooks();
        

        // Check for bot activity on init
        add_action('init', [$this, 'detect_bot_activity']);
        
        // Execute page rules
        add_action('init', [$this, 'run_page_rules']);
        
        
        //run routine checks maintenance 
        $this->run_routine_checks();
        
        //Load plugin scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

   
        // Register AJAX actions for both logged-in and non-logged-in users
        add_action('wp_ajax_verify_recaptcha_score', [$this, 'verify_recaptcha_score_ajax_handler'] );
        add_action('wp_ajax_nopriv_verify_recaptcha_score', [$this, 'verify_recaptcha_score_ajax_handler']);
 

    }

    // Load dependencies and required files
    private function load_dependencies() {
        include_once(plugin_dir_path(__FILE__) . 'admin/advanced-rules.php');
        
        include_once(plugin_dir_path(__FILE__) . 'includes/rule-check.php'); 
        include_once(plugin_dir_path(__FILE__) . 'includes/class-wp-bot-blocker-helper.php') ;
        require_once WP_BOT_BLOCKER_DIR. 'includes/class-wp-bot-blocker-headers.php';
        
   
        //include_once(plugin_dir_path(__FILE__) . 'includes/logging.php');
        
  // Load  global helpers and api
  $this->helper = new WP_Bot_Blocker_Helper(); 
  $this->honeypot = new WPBotBlockerHoneyPotAPI(); 
  
   if (is_admin()) {
    
    new WP_Bot_Blocker_Admin();
   
    new WPBotBlocker_Advanced_Rules($this);
   } 
   // Load front end dependencies 
   // these departments are ONLY at frontend
   if(! is_admin())
   {
                 
       $this->rulecheck = new WPBotBlocker_Rule_Check($this);
       $this->detector = new WP_Bot_Blocker_Detection(); 
   }
   
   
 }
    
    // Register plugin hooks
    public function register_hooks() {
        
       // Register activation and deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate_plugin']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate_plugin']);
    
    register_activation_hook(__FILE__, function() {
    WP_Bot_Blocker_Logging::schedule_log_cleanup();
});

register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('wp_bot_blocker_cleanup_logs');
});
    
        
  }


    // Plugin activation tasks
    public function activate_plugin() {
     $adv = new WPBotBlocker_Advanced_Rules();
     
    $adv->create_tables() ;
    
    WP_Bot_Blocker_Logging::create_log_table();
    WP_Bot_Blocker_Admin::create_blacklist_table();
    WP_Bot_Blocker_Admin::create_traffic_logs_table();
    }

    // Plugin deactivation tasks
    public function deactivate_plugin() {
        // Optionally clean up or reset settings
        
       //TEMPORARY DEBUG
       /* global $wpdb;
        $table_name = $wpdb->prefix . 'wp_bot_blocker_rules';

        $charset_collate = $wpdb->get_charset_collate();
       
        $sqli = "DROP TABLE IF EXISTS $table_name ";
        $wpdb->get_results($sqli);
        */

    }

   // Detect bot activity on init
   public function detect_bot_activity() {
        if (is_admin()) return;
        
       $this->detector->run_detection();
 
    }
    
    
    // Run execute page rules 
   public function run_page_rules()
   {
       if (is_admin()) return;

       $this->rulecheck->execute_rules();
   }
   
   // run routine checks
   public function run_routine_checks()
   {
       add_action('wp_bot_blocker_cleanup_logs', array('WP_Bot_Blocker_Logging', 'clear_old_logs'));


       add_action('wp', array('WP_Bot_Blocker_Traffic', 'log_visit'));

   }
   
 
 public function enqueue_scripts(){
     
    // $this->enqueue_recaptcha_script();
     
 }
   
   
  private function enqueue_recaptcha_script() {
    $recaptcha_api = new ReCAPTCHAv3API();
    $site_key = $recaptcha_api->get_site_key();
   

    if (!empty($site_key)) { 
        // Enqueue reCAPTCHA v3 script
        wp_enqueue_script('recaptcha-v3', 'https://www.google.com/recaptcha/api.js?render=' . $site_key, [], null, true);

        // Inline script to generate reCAPTCHA token and send it via AJAX
        wp_add_inline_script('recaptcha-v3', "
            grecaptcha.ready(function() {
                grecaptcha.execute('$site_key', {action: 'page_load'}).then(function(token) {
                    // Send token to the server via AJAX
                    fetch('" . admin_url('admin-ajax.php') . "?action=verify_recaptcha', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ recaptcha_token: token }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('reCAPTCHA check:', data.message);
                        if (!data.success) {
                            // Take action if bot detected, like redirecting to a warning page
                            window.location.href = '/block-page-template.php';
                        }
                    });
                });
            });
        ");
    }
}


public function verify_recaptcha_score_ajax_handler() {
    // Get the JSON input and decode
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $token = isset($data['recaptcha_token']) ? sanitize_text_field($data['recaptcha_token']) : '';

    if (empty($token)) {
        wp_send_json(['success' => false, 'message' => 'No reCAPTCHA token provided.']);
        wp_die();
    }

    // Verify the token with the ReCAPTCHAv3API
    $recaptcha_api = new ReCAPTCHAv3API();
    $threshold = 0.0; // Set threshold score for bot detection (adjust as needed)

    // Check if the request score meets the threshold
    if ($recaptcha_api->verify_response($token, $threshold)) {
        // Score meets threshold, likely a human
        wp_send_json(['success' => true, 'message' => 'User verified as likely human.']);
    } else {
        // Score below threshold, likely a bot
        wp_send_json(['success' => false, 'message' => 'Bot detected.']);
    }

    wp_die();
}


   
}

// Initialize the plugin
WPBotBlocker::get_instance();


