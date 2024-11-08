<?php
if (!defined('ABSPATH')) exit;

class WP_Bot_Blocker_ReCaptcha {

    public static function display_recaptcha() {
        $site_key = get_option('wp_bot_blocker_recaptcha_site_key');
        echo $site_key ;
        if ($site_key) {
            echo '<div class="g-recaptcha" data-sitekey="' . esc_attr($site_key) . '"></div>';
            echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        }
    }

    public static function validate_recaptcha($token) {
        $secret_key = get_option('wp_bot_blocker_recaptcha_secret_key');
        if (!$secret_key || !$token) {
            return false;
        }

        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
            'body' => array(
                'secret' => $secret_key,
                'response' => $token
            )
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $response_body = wp_remote_retrieve_body($response);
        $result = json_decode($response_body, true);
        
        return isset($result['success']) && $result['success'] === true;
    }
}
?>
