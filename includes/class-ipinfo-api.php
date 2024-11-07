<?php

if (!class_exists('IPInfoAPI')) {
    class IPInfoAPI {
        private $api_key;
        private $base_url = 'https://ipinfo.io/';

        public function __construct($api_key) {
            $this->api_key = $api_key;
        }

        /**
         * Get the full IP info data (country, region, city, etc.)
         *
         * @param string $ip IP address to look up.
         * @return array|false Returns an associative array with IP info or false on failure.
         */
        public function get_ip_info($ip) {
            $url = $this->build_url($ip);
            $response = $this->make_request($url);

            return $response ? json_decode($response, true) : false;
        }

        /**
         * Get the country code from an IP address.
         *
         * @param string $ip IP address to look up.
         * @return string|false Returns country code as a string (e.g., "US") or false on failure.
         */
        public function get_country($ip) {
            $url = $this->build_url($ip, 'country');
            $response = $this->make_request($url);

            return $response ? trim($response) : false;
        }

        /**
         * Get the city name from an IP address.
         *
         * @param string $ip IP address to look up.
         * @return string|false Returns city name as a string or false on failure.
         */
        public function get_city($ip) {
            $url = $this->build_url($ip, 'city');
            $response = $this->make_request($url);

            return $response ? trim($response) : false;
        }

        /**
         * Get the region name from an IP address.
         *
         * @param string $ip IP address to look up.
         * @return string|false Returns region name as a string or false on failure.
         */
        public function get_region($ip) {
            $url = $this->build_url($ip, 'region');
            $response = $this->make_request($url);

            return $response ? trim($response) : false;
        }

        /**
         * Build the API URL based on IP and endpoint.
         *
         * @param string $ip IP address.
         * @param string $endpoint Specific endpoint (e.g., "country", "city"), optional.
         * @return string Complete API URL.
         */
        private function build_url($ip, $endpoint = '') {
            $url = $this->base_url . $ip;
            if ($endpoint) {
                $url .= '/' . $endpoint;
            }
            $url .= '?token=' . $this->api_key;

            return $url;
        }

        /**
         * Make the API request.
         *
         * @param string $url The API endpoint URL.
         * @return string|false The API response body or false on error.
         */
        private function make_request($url) {
            $response = wp_remote_get($url);

            if (is_wp_error($response)) {
                return false;
            }

            $body = wp_remote_retrieve_body($response);

            return $body;
        }
    }
}
