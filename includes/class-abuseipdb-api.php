<?php
if (!defined('ABSPATH')) exit;

if (!class_exists('AbuseIPDBAPI')) {
    class AbuseIPDBAPI {
        private $api_key;
        private $base_url = 'https://api.abuseipdb.com/api/v2/check';

        public function __construct() {
            $this->api_key = '8fcfe61bfad327cb5a8ed618a8de608200e76c14984829006281bf9fd260d8675e3601d41900f87e';
        }

        /**
         * Check if an IP address has been reported for abuse.
         *
         * @param string $ip IP address to check.
         * @return array|false Returns an array with abuse information or false on failure.
         */
        public function check_ip($ip) {
            if (empty($this->api_key)) {
                return false;
            }
            
            //Check Cached response 
             $cache_key = 'wp_bot_blocker_abuseipdb_'.$ip;
             $cache = get_transient($cache_key) ;
            
            if(! (false === $cache) ) 
                {  return $cache; } 
            
            $url = $this->base_url . '?ipAddress=' . urlencode($ip) . '&maxAgeInDays=90';

            $response = wp_remote_get($url, [
                'headers' => [
                    'Key' => $this->api_key,
                    'Accept' => 'application/json',
                ],
            ]);

            if (is_wp_error($response)) {
               return false;
              
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
           

            if (isset($body['data'])) {
                
                // cache response
                set_transient($cache_key, $body['data'], 2 * DAY_IN_SECONDS );
                
                return $body['data'];
            }

            return false;
        }

        /**
         * Determine if an IP address is likely malicious based on Abuse Confidence Score.
         *
         * @param string $ip IP address to check.
         * @param int $threshold Confidence score threshold (0-100) to consider an IP as malicious.
         * @return bool Returns true if the IP is considered malicious, false otherwise.
         */
        public function is_malicious($ip, $threshold = 50) {
            $data = $this->check_ip($ip);
            return isset($data['abuseConfidenceScore']) && $data['abuseConfidenceScore'] >= $threshold;
        }
    }
}
 
