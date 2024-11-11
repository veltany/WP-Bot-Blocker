<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WP_Bot_Blocker_Detection extends WPBotBlocker {
    private $score_threshold;
    private $excluded_bots;
    private $enable_honeypot;
    private $rate_limit_threshold;
    private $rate_limit_window;
    private $rate_limit_block_duration;

    public function __construct() {
        $this->score_threshold = (int) get_option('wp_bot_blocker_score_threshold', 5);
        $this->excluded_bots = explode(',', get_option('wp_bot_blocker_excluded_bots', 'Googlebot, Bingbot'));
        $this->enable_honeypot = (bool) get_option('wp_bot_blocker_enable_honeypot');
        $this->rate_limit_threshold = (int) get_option('wp_bot_blocker_rate_limit_threshold', 10);
        $this->rate_limit_window = (int) get_option('wp_bot_blocker_rate_limit_window', 60);
        $this->rate_limit_block_duration = (int) get_option('wp_bot_blocker_rate_limit_block_duration', 3600 );
    }

    public function run_detection() {
        

       $bot_blocker_headers = new WP_Bot_Blocker_Headers();

        $ip_address = $bot_blocker_headers->get_ip();
        $user_agent = $bot_blocker_headers->get_user_agent();



        // Proceed with other bot detection checks
       
        // reCaptcha v3 not verified ? 
        if ($this->is_recaptchav3_failed($ip_address))  
             {
              $this->block_request($ip_address, 'bot_detection_recaptcha', $user_agent);
             } 
            
        
        
         // Check if IP is rate-limited
        if ($this->is_rate_limited($ip_address)) { 
          if (!$this->verify_recaptcha()) 
          {
              $this->block_request($ip_address, 'rate_limit', $user_agent);
          } 
        }

        
        
        
        //$this->check_honey_pot($ip_address ) ;
       // $honeypot = new WPBotBlockerHoneyPotAPI();
      //  $help = new WP_Bot_Blocker_Helper ();
       // $help->log("Bot Data: ". $honeypot->get_bot_data('185.185.217.76')) ;
       
        
        // Skip if the bot is excluded (e.g., Googlebot)
        if ($this->is_excluded_bot($user_agent)) return;

        // If bot score threshold is exceeded, block due to bot detection
        if ($this->calculate_bot_score($user_agent, $ip_address) >= $this->score_threshold) {
            if (!$this->verify_recaptcha()) {
                $this->block_request($ip_address, 'bot_detection', $user_agent);
            }
        }
        
    }
    
    
    private function is_recaptchav3_failed($ip_address) 
    { 
       // If Recaptcha V3 not enabled
       if(! (get_option('wp_bot_blocker_enable_recaptchav3') === '1')) 
       { return false ;} 
       
      // is whitelisted
      if ( get_transient('wp-bot-blocker-captchav3_whitelist'.md5($ip_address)) ) 
      { return false ; } 
        
      // Check if the IP is blocked by recaptcha v3 check
       if ( get_transient('wp_bot_blocker_blocked_recaptcha' . md5($ip_address)))
        {
            
           //then Captcha v2 challenge not verified 
             if (!$this->verify_recaptcha()) 
             {
              return true;
             } 
              else
              {
                  //Captcha v2 verified, unblock 
                  delete_transient('wp_bot_blocker_blocked_recaptcha' . md5($ip_address)  ) ;
                  
                  //Since passed Captcha Challenge, white-list for now 
                  set_transient('wp-bot-blocker-captchav3_whitelist'. md5($ip_address), true, 10 * MINUTE_IN_SECONDS);
                  
                  return false ;
              }
         } 
         return false ;
        
        
    }





    private function is_rate_limited($ip_address) {
      
      // first, check if there is active
      // block
        if ( get_transient("wp_bot_blocker_blocked_$ip_address")) 
        {
            return true;
        }
      // then none
      
        $request_count = get_transient("wp_bot_blocker_rate_limit_$ip_address") ?: 0;

        if ($request_count >= $this->rate_limit_threshold) {
          
            set_transient("wp_bot_blocker_blocked_$ip_address", true, $this->rate_limit_block_duration);
            return true;
        }

        set_transient("wp_bot_blocker_rate_limit_$ip_address", $request_count + 1, $this->rate_limit_window);
        return false;
    }

    public function block_request($ip_address, $reason, $user_agent="" ) {
    $message = ($reason === 'rate_limit') 
        ? get_option('wp_bot_blocker_rate_limit_message', 'Too many requests. Please try again later.') 
        : get_option('wp_bot_blocker_bot_detection_message', 'Access Denied. Please verify you are human.');

    // Log the attempt with the reason
    WP_Bot_Blocker_Logging::log_attempt($ip_address, $reason, $user_agent);

    // Display the block page with the appropriate message
    status_header(429);
    include WP_BOT_BLOCKER_DIR . '/block-page-template.php';
    exit;
}


    private function is_excluded_bot($user_agent) { 
        
        if(count($this->excluded_bots)==1 & empty($this->excluded_bots[0])) return false ;
        
        foreach ($this->excluded_bots as $bot_name) {
            
            if (stripos($user_agent, trim($bot_name)) !== false) {
                return true;
            }
        } 
        return false;
    }

    private function calculate_bot_score($user_agent, $ip_address) {
        $bot_score = 0;

        if ($this->is_bot_user_agent($user_agent)) {
            $bot_score += 3;
        }

        if ($this->rate_limit_exceeded($ip_address)) {
            $bot_score += 3;
        }
        
        //Check abuseIPD
        $abuseipdb_api = new AbuseIPDBAPI();
        
      if ($abuseipdb_api->is_malicious($ip_address , 50)) {
             $bot_score += 5;
         }
        
        if ($this->enable_honeypot) {
            $honey_pot_score = $this->check_honey_pot($ip_address);
            if ($honey_pot_score) {
                WP_Bot_Blocker_Logging::log_attempt($ip_address, $user_agent, 'Honey Pot Block');
                $this->add_to_blacklist($ip_address, 'Flagged by Project Honey Pot');
                $bot_score = 10;
            }
        }

        return $bot_score;
    }

    private function is_blacklisted($ip_address) {
        return WP_Bot_Blocker_Reputation::check_blacklist($ip_address);
    }

    private function is_bot_user_agent($user_agent) {
        $monitor_crawlers = get_option('wp_bot_blocker_monitor_crawlers', true);
        $monitor_scrapers = get_option('wp_bot_blocker_monitor_scrapers', true);
        $monitor_spammers = get_option('wp_bot_blocker_monitor_spammers', true);

        $bad_user_agents = [];

        if ($monitor_crawlers) $bad_user_agents[] = '*bot*';
        if ($monitor_scrapers) $bad_user_agents[] = '*crawl*';
        if ($monitor_spammers) $bad_user_agents[] = '*spider*';

        foreach ($bad_user_agents as $bad_ua) {
            
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($bad_ua, '/')) . '$/i';
            
            if (preg_match($pattern, $user_agent ) ) {
                return true;
            }
        }

        return false;
    }


    private function rate_limit_exceeded($ip_address) {
        $request_limit = 10;
        $time_window = 10;

        $request_log = get_transient('wp_bot_blocker_requests_' . $ip_address) ?: [];

        $request_log = array_filter($request_log, function($timestamp) use ($time_window) {
            return (time() - $timestamp) <= $time_window;
        });

        if (count($request_log) >= $request_limit) {
            return true;
        }

        $request_log[] = time();
        set_transient('wp_bot_blocker_requests_' . $ip_address, $request_log, $time_window);

        return false;
    }

    private function check_honey_pot($ip_address) { 
        $api_key = get_option('wp_bot_blocker_honeypot_api_key');
        
        if (!$api_key) return false;
       
        $ip_parts = explode('.', $ip_address);
        if (count($ip_parts) != 4) return false; // Invalid IP format

        $query = $api_key . '.' . implode('.', array_reverse($ip_parts)) . '.dnsbl.httpbl.org';
        $result = gethostbyname($query);
        
        if ($result === $query || strpos($result, '127.') !== 0) {
            return false; // Not in the Honey Pot database
        }

        // Parse response (127.xx.yy.zz): xx=threat score, yy=type
        list(, $threat_score, $type) = explode('.', $result);

        // Block if threat score is high or type indicates a known bot
        return ($threat_score > 50 || $type == 3);
    }

    private function add_to_blacklist($ip_address, $reason) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_bot_blacklist';

        $wpdb->insert($table_name, array(
            'ip_address' => sanitize_text_field($ip_address),
            'reason' => sanitize_text_field($reason),
            'added_time' => current_time('mysql')
        ), array('%s', '%s', '%s'));
    }

private function verify_recaptcha() {
        $token = isset($_POST['g-recaptcha-response']) ? sanitize_text_field($_POST['g-recaptcha-response']) : '';
        
    // Check if there's a cached verification result for this IP
  /* $headers = new WP_Bot_Blocker_Headers();
   $ip_address = $headers->get_ip();
    $cache_key = 'wp_bot_blocker_verify_captcha_' . md5($ip_address);
    $cached_result = get_transient($cache_key);

    if ($cached_result !== false) {
        // Use the cached result
        return $cached_result;
    }
     */   $return = WP_Bot_Blocker_ReCaptcha::validate_recaptcha($token);
    
        // Cache the return 
          //  set_transient($cache_key, $return, 5 * MINUTE_IN_SECONDS);
            
        return $return ;

        
        
    }

    
 
}

