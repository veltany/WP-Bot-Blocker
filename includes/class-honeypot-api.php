<?php

if (!class_exists('WPBotBlockerHoneyPotAPI')) {
    
    class WPBotBlockerHoneyPotAPI {
        private $api_key;
        private $base_url = 'https://dnsbl.httpbl.org';

        public function __construct() {
            // Retrieve the API key from the plugin settings
            $this->api_key = get_option('wp_bot_blocker_honeypot_api_key', '');
        }

        /**
         * Check if an IP address is a known bot.
         *
         * @param string $ip IP address to check.
         * @return bool Returns true if the IP is a bot, false otherwise.
         */
        public function is_bot($ip) {
            $data = $this->get_bot_data($ip);
            return isset($data['threat']) && $data['threat'] > 0;
        }

        /**
         * Get the bot score for an IP address.
         *
         * @param string $ip IP address to check.
         * @return int|false Bot score (0-255) or false if data is unavailable.
         */
        public function get_bot_score($ip) {
            $data = $this->get_bot_data($ip);
            return isset($data['threat']) ? (int) $data['threat'] : false;
        }

        /**
         * Get the bot type (e.g., search engine, spammer) for an IP address.
         *
         * @param string $ip IP address to check.
         * @return string|false Bot type description or false if data is unavailable.
         */
        public function get_bot_type($ip) {
            $data = $this->get_bot_data($ip);
            return isset($data['type']) ? $this->parse_bot_type($data['type']) : false;
        }

        /**
         * Retrieve all available bot data for an IP address.
         *
         * @param string $ip IP address to check.
         * @return array|false Returns associative array of bot data or false on failure.
         */
        public function get_bot_data($ip) {
            // Ensure we have a valid API key
            if (empty($this->api_key)) {
                return false;
            }

            $url = $this->build_url($ip);
            $response = $this->make_request($url);

            if (!$response) return false;

            // Parse response into bot data
            return $this->parse_response($response);
        }

        /**
         * Build the API URL for a given IP address.
         *
         * @param string $ip IP address.
         * @return string Complete API URL.
         */
        private function build_url($ip) {
            return "{$this->base_url}{$this->api_key}/{$ip}/";
        }

        /**
         * Make the HTTP request to the Honey Pot API.
         *
         * @param string $url The API endpoint URL.
         * @return string|false The API response body or false on error.
         */
        private function make_request($url) {
            $response = wp_remote_get($url);

            if (is_wp_error($response)) {
                
                return false;
            }

            return wp_remote_retrieve_body($response);
        }

        /**
         * Parse the API response into structured data.
         *
         * @param string $response Raw response string.
         * @return array Parsed data (e.g., threat score, type).
         */
        private function parse_response($response) {
            $data_parts = explode('.', $response);

            if (count($data_parts) < 4) return false;

            return [
                'last_activity' => (int) $data_parts[1],
                'threat' => (int) $data_parts[2],
                'type' => (int) $data_parts[3],
            ];
        }

        /**
         * Interpret the bot type based on Honey Pot API response code.
         *
         * @param int $type_code Bot type code from Honey Pot.
         * @return string Human-readable bot type.
         */
        private function parse_bot_type($type_code) {
            switch ($type_code) {
                case 0: return 'Search Engine';
                case 1: return 'Suspicious';
                case 2: return 'Harvester';
                case 4: return 'Comment Spammer';
                default: return 'Unknown';
            }
        }
    }
}
?>
