<?php
if (!defined('ABSPATH')) exit;

class WPBotBlocker_Rule_Check {
    private $table_name;
    private $wpb ;
    private $headers;
    private $ip;
    private $user_agent ;

    public function __construct(WPBotBlocker $WPBotBlocker) {
        
        $this->wpb = $WPBotBlocker ;
        
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wp_bot_blocker_rules';
        
       $this->headers = new WP_Bot_Blocker_Headers();

        $this->ip = $this->headers->get_ip();
        $this->user_agent = $this->headers->get_user_agent();
       
    }

    // Main method to check rules and apply actions
    public function execute_rules() {
     
        $rules = $this->get_rules();
        
        foreach ($rules as $rule) {
            if ($this->check_condition($rule)) {
                
                $this->apply_action($rule);
                
            }
        }
    }

    // Fetch all rules from the database
    private function get_rules() {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM $this->table_name");
    }

    // Check if a rule's condition matches the current request
    private function check_condition($rule) {
    $condition_value = $rule->condition_value;

    // Convert the condition value with '*' into a regex pattern
    $pattern = '/^' . str_replace('\*', '.*', preg_quote($condition_value, '/')) . '$/i';

    switch ($rule->type) {
        case 'ip':
            return preg_match($pattern, $this->ip);
        
        case 'user_agent':
            return preg_match($pattern, $this->user_agent );
        
        case 'country':
            $country = $this->get_country_by_ip($this->ip);
            return ($country === $condition_value ) ;
        
        case 'request_uri':
            return preg_match($pattern, $_SERVER['REQUEST_URI']);
        
        case 'full_url':
            return ($this->get_current_url === $condition_value );
                
        case 'known_bot':
            return false ;
        
        
        case 'query_strings':
            return preg_match($pattern, $_SERVER['QUERY_STRING'] ?? '');
        
        // Additional cases for other rule types can be added here
    }
    return false;
}
 
    // Apply the specified action (e.g., block the request)
    private function apply_action($rule) {
     if ($rule->action === 'block') 
     {
      // Check if user passed captcha
       if (!$this->verify_recaptcha()) 
      {
       $this->wpb->detector->block_request($this->ip, 
            "Rule: $rule->rule_name >> $rule->action", 
            $this->user_agent );
      } 
     }
     if ($rule->action === 'redirect')
     {
      // log this redirect
      WP_Bot_Blocker_Logging::log_attempt($this->ip, "Rule: $rule->rule_name >> $rule->action", $this->user_agent);

       wp_redirect($rule->redirect_url, 302,'WP Bot Blocker');
     }
        // Additional actions can be added here as needed
    }

    // Helper function to get country by IP (stubbed, implement as needed)
  private function get_country_by_ip($ip) 
    {
        include_once WP_BOT_BLOCKER_DIR . '/includes/class-geoplugin-api.php';
        
       $country_class = new GeoPluginAPI();
       $country = $country_class->get_geolocation($ip);
       
       if($country)
       return $country["country_code"];
       else
        return false ;
    }
    
 private function get_current_url() {
    $protocol = is_ssl() ? 'https://' : 'http://';
    return ($protocol) . $this->headers->get_header('HTTP_HOST'). $this->hraders->get_header('REQUEST_URI') ;
}

private function verify_recaptcha() {
        $token = isset($_POST['g-recaptcha-response']) ? sanitize_text_field($_POST['g-recaptcha-response']) : '';
        return WP_Bot_Blocker_ReCaptcha::validate_recaptcha($token);
    }



}
