<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('ReCAPTCHAv3API')) {
    class ReCAPTCHAv3API {
        private $site_key;
        private $secret_key;
        private $api_url = 'https://www.google.com/recaptcha/api/siteverify';

        public function __construct() {
            $this->site_key = get_option('wp_bot_blocker_recaptcha_site_key');
            
            $this->secret_key = get_option('wp_bot_blocker_recaptcha_secret_key');
        }

        /**
         * Get the reCAPTCHA Site Key
         *
         * @return string The reCAPTCHA site key.
         */
        public function get_site_key() {
            return $this->site_key;
        }

        /**
         * Verify the reCAPTCHA response from the user.
         *
         * @param string $token The token provided by reCAPTCHA v3 from the frontend.
         * @param float $threshold Score threshold to determine if the user is likely a bot.
         * @return bool True if the user is likely not a bot, false otherwise.
         */
        public function verify_response($token, $threshold = 0.5) {
            if (empty($this->secret_key)) {
                return false;
            }

            $response = $this->make_request($token);

            if (!$response || empty($response['success']) || $response['score'] < $threshold) {
                return false;
            }

            return true;
        }

        /**
         * Make the HTTP request to verify the reCAPTCHA response.
         *
         * @param string $token The reCAPTCHA response token from the user.
         * @return array|false Returns the decoded JSON response from Google or false on failure.
         */
        private function make_request($token) {
            $response = wp_remote_post($this->api_url, [
                'body' => [
                    'secret' => $this->secret_key,
                    'response' => $token,
                ]
            ]);

            if (is_wp_error($response)) {
                return false;
            }

            $body = wp_remote_retrieve_body($response);
            return json_decode($body, true);
        }
    }
}
?>
