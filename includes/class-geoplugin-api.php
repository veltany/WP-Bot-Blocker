<?php
if (!defined('ABSPATH')) exit;

if (!class_exists('GeoPluginAPI')) {
    class GeoPluginAPI {
        private $base_url = 'http://www.geoplugin.net/json.gp';

        /**
         * Get geolocation data for a specific IP address.
         *
         * @param string $ip IP address to lookup.
         * @return array|false Associative array with geolocation data or false on failure.
         */
        public function get_geolocation($ip) {
            
             //Check Cached response 
             $cache_key = 'wp_bot_blocker_geoip_'.$ip;
             $cache = get_transient($cache_key) ;
               if(! (false === $cache) ) 
                {  return $cache; } 
             
            $url = $this->base_url . '?ip=' . urlencode($ip);
            $response = wp_remote_get($url);

            if (is_wp_error($response)) {
                return false;
            }

            $data = json_decode(wp_remote_retrieve_body($response), true);

            if (isset($data['geoplugin_status']) && $data['geoplugin_status'] == 200) {
                $return = [
                    'continent' => $data['geoplugin_continentName'],
                    'country' => $data['geoplugin_countryName'],
                    'country_code' => $data['geoplugin_countryCode'],
                    'region' => $data['geoplugin_region'],
                    'city' => $data['geoplugin_city'],
                    'latitude' => $data['geoplugin_latitude'],
                    'longitude' => $data['geoplugin_longitude'],
                    'currency_code' => $data['geoplugin_currencyCode'],
                    'currency_symbol' => $data['geoplugin_currencySymbol'],
                ];
                 // cache response
                set_transient($cache_key, $return, DAY_IN_SECONDS );
                return $return;
                
                
            }

            return false;
        }

  /**
         * Get list of all countries, optionally filtered by country code or name.
         *
         * @param string $filter Optional filter for country code or name.
         * @return array List of countries, optionally filtered.
         */
   public function get_countries($filter = '') {
         
        // to load only when needed 
        require_once WP_BOT_BLOCKER_DIR . 'includes/class-wp-bot-blocker-country.php';
        $list = new  Class_WP_Bot_Blocker_Country();
       $countries = $list->get_country_list();
            if ($filter) {
                $filter = strtolower($filter);
                $countries = array_filter($countries, function($country) use ($filter) {
                    return stripos($country['name'], $filter) !== false || stripos($country['code'], $filter) !== false;
                });
            }
            return array_values($countries);
        }

        /**
         * Get list of all continents.
         *
         * @return array List of continents with code and name.
         */
        public function get_continents() {
            return [
                ['code' => 'AF', 'name' => 'Africa'],
                ['code' => 'AN', 'name' => 'Antarctica'],
                ['code' => 'AS', 'name' => 'Asia'],
                ['code' => 'EU', 'name' => 'Europe'],
                ['code' => 'NA', 'name' => 'North America'],
                ['code' => 'OC', 'name' => 'Oceania'],
                ['code' => 'SA', 'name' => 'South America']
            ];
        }

        /**
         * Get list of states/provinces for a specified country.
         *
         * @param string $country_code Country code to filter states by.
         * @return array|false List of states if available, false if not found.
         */
        public function get_states($country_code) {
            $country_code = strtoupper($country_code);
            $states = $this->get_states_by_country();

            return $states[$country_code] ?? false;
        }


        /**
         * Retrieve country name by its ISO code.
         *
         * @param string $code Country ISO code.
         * @return string|false Country name if found, false otherwise.
         */
  public function get_country_by_code($code)
  {
            $code = strtoupper($code);
            foreach ($this->get_countries() as $country) {
                if ($country['code'] === $code) {
                    return $country['name'];
                }
            }
            return false;
  }

   

        /**
         * Internal method to provide a hard-coded list of states/provinces by country code.
         * This list can be expanded as needed or replaced by an API source if available.
         *
         * @return array List of states by country code.
         */
        private function get_states_by_country() {
            return [
                'US' => [
                    ['code' => 'CA', 'name' => 'California'],
                    ['code' => 'TX', 'name' => 'Texas'],
                    ['code' => 'NY', 'name' => 'New York'],
                    // Add more states as needed
                ],
                'CA' => [
                    ['code' => 'ON', 'name' => 'Ontario'],
                    ['code' => 'QC', 'name' => 'Quebec'],
                    ['code' => 'BC', 'name' => 'British Columbia'],
                    // Add more provinces as needed
                ],
                // Add other countries and their states as needed
            ];
        }
    }
}

