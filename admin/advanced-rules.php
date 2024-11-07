<?php
class WPBotBlocker_Advanced_Rules {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wp_bot_blocker_rules';
   
   add_action('admin_menu', array($this, 'add_submenu_page'));
    }

    // Add the submenu page under the main Bot Blocker menu
    public function add_submenu_page() {
        add_submenu_page(
            'wp-bot-blocker',
            'Block Rules',
            'Block Rules',
            'manage_options',
            'wp-bot-blocker-advanced-rules',
            [$this, 'render_advanced_rules_page']
        );
    }

    // Load and render the rules page
  public function render_advanced_rules_page() {
      
        if (isset($_POST['new_rule'])) {
            $this->add_new_rule();
        }
        
        if (isset($_GET['delete_rule'])) {
            $this->delete_rule(intval($_GET['delete_rule']));
        }

        $rules = $this->get_rules();
        
        include_once WP_BOT_BLOCKER_DIR. '/includes/class-geoplugin-api.php';
        
        $country_class = new GeoPluginAPI ();
        $countries = $country_class->get_countries(); // Fetch the full list of countries
        $bot_cat = 
        [
            [
             'type' => 'search_bot', 
             'name' => 'Search Bots'
            ], 
            [
             'type' => 'ai_scraper', 
             'name' => 'AI Scraper and Machine Learning'
            ], 
            [
             'type' => 'spider_bot', 
             'name' => 'Spiders and Spamming Bots'
            ]
        ];

        // Render the HTML view
        include plugin_dir_path(__FILE__) . 'views/advanced-rules-view.php';
    }

    // Add a new rule to the database
    private function add_new_rule() {
        global $wpdb;
        $wpdb->insert($this->table_name, [
            'rule_name' => sanitize_text_field($_POST['rule_name']),
            'type' => sanitize_text_field($_POST['rule_type']),
            'condition_value' => sanitize_text_field($_POST['condition_value']),
            'action' => sanitize_text_field($_POST['rule_action']),
            'redirect_url' => sanitize_text_field($_POST['redirect_url']),
        ]);
    }

    // Delete a rule from the database
    private function delete_rule($id) {
        global $wpdb;
        $wpdb->delete($this->table_name, ['id' => $id]);
    }

    // Retrieve all rules from the database
  private function get_rules() {
        global $wpdb;
        
    //update table
   $row = $wpdb->get_results("SELECT COLUMN_NAME  FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '$this->table_name' AND column_name = 'redirect_url'");
    

    if(empty($row)){
   $wpdb->query("ALTER TABLE $this->table_name ADD redirect_url VARCHAR(100) NOT NULL DEFAULT ''");
        
    }
   
        return $wpdb->get_results("SELECT * FROM $this->table_name");
    }
    
 
 // Plugin activation tasks
public function create_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_bot_blocker_rules';

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        rule_name varchar(50) NOT NULL,
        type varchar(50) NOT NULL,
        condition_value varchar(255) NOT NULL,
        action varchar(10) NOT NULL,
        redirect_url varchar(100) NULL, 
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

}

