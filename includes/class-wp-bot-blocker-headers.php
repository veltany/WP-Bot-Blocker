<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


class WP_Bot_Blocker_Headers {

    /**
     * @var string Custom header for the user's CDN (set by the user in plugin settings).
     */
    private $custom_cdn_header;

    /**
     * @var string Custom user agent header for the user's CDN (set by the user in plugin settings).
     */
    private $custom_user_agent_header;

    /**
     * Constructor.
     *
     * @param string $custom_cdn_header Custom header set by the user in plugin settings.
     * @param string $custom_user_agent_header Custom user agent header set by the user in plugin settings.
     */
    public function __construct()
    {
        $this->custom_cdn_header = '' ;
        $this->custom_user_agent_header = '' ;
    }



    /**
     * Get user request headers.
     *
     * @return array Associative array of request headers.
     */
    public function get_request_headers() {
        if ( function_exists( 'apache_request_headers' ) ) {
            // Use apache_request_headers() if available
            $headers = apache_request_headers();
        } else {
            // Fallback method for getting headers
            $headers = [];
            foreach ( $_SERVER as $key => $value ) {
                if ( substr( $key, 0, 5 ) === 'HTTP_' ) {
                    $header = str_replace( ' ', '-', ucwords( str_replace( '_', ' ', strtolower( substr( $key, 5 ) ) ) ) );
                    $headers[ $header ] = $value;
                }
            }
        }

        return $headers;
    }

    /**
     * Check for specific header conditions (e.g., detect bot-related headers).
     *
     * @param string $header_name The name of the header to check.
     * @return string|null Returns the header value if found, or null otherwise.
     */
    public function get_header( $header_name ) 
    {
        
       if($header_name == 'REMOTE_ADDR' )
          {
              return $this->get_ip();
          } 
          
       if($header_name == 'HTTP_USER_AGENT' )
         { 
             return $this->get_user_agent();
         } 
         
        $headers = $this->get_request_headers();
         
        return isset( $headers[ $header_name ] ) ? $headers[ $header_name ] : null;
    }



    /**
     * Get the connecting IP address of the user.
     *
     * @return string|null The IP address or null if not found.
     */
    public function get_ip() {
        // List of headers for popular CDNs
        $cdn_headers = [
            'HTTP_CF_CONNECTING_IP',       // Cloudflare
            'HTTP_X_REAL_IP',              // Bunny.net
            'HTTP_X_FORWARDED_FOR',        // Generic/used by many CDNs
            'HTTP_TRUE_CLIENT_IP',         // Akamai
            'HTTP_X_CLIENT_IP',            // Fastly, some proxies
            'HTTP_X_FORWARDED',            // Generic, some proxies
            'HTTP_X_CLUSTER_CLIENT_IP',    // Rackspace, other proxies
            'HTTP_X_ORIGINATING_IP',       // Some ISPs, generic
            'HTTP_X_FORWARDED_FOR',        // Amazon CloudFront, common proxy
            'HTTP_FORWARDED_FOR',          // Standard forwarding header
            'HTTP_FORWARDED',              // Standard forwarding header
        ];

        // Check each CDN header in order
        foreach ( $cdn_headers as $header ) {
            if ( ! empty( $_SERVER[ $header ] ) ) {
                $ip_list = explode( ',', $_SERVER[ $header ] ); // In case of multiple IPs
                return trim( $ip_list[0] ); // Return the first IP, typically the client’s IP
            }
        }

        // Check the custom header if set by the user
        if ( ! empty( $this->custom_cdn_header ) && ! empty( $_SERVER[ $this->custom_cdn_header ] ) ) {
            $ip_list = explode( ',', $_SERVER[ $this->custom_cdn_header ] );
            return trim( $ip_list[0] );
        }

        // Fallback to REMOTE_ADDR if no CDN headers are found
        return ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    /**
     * Get the original user agent of the client.
     *
     * @return string|null The user agent or null if not found.
     */
    public function get_user_agent() {
        // Common user agent headers for CDNs and proxies
        $user_agent_headers = [
            'HTTP_USER_AGENT',            // Standard header for user agent
            'HTTP_X_DEVICE_USER_AGENT',   // Some proxies and CDNs
            'HTTP_X_ORIGINAL_USER_AGENT', // Some proxies and CDNs
            'HTTP_X_OPERAMINI_PHONE_UA',  // Opera Mini
            'HTTP_X_UA_COMPATIBLE',       // Compatibility user agent (e.g., IE)
        ];

        // Check each user agent header in order
        foreach ( $user_agent_headers as $header ) {
            if ( ! empty( $_SERVER[ $header ] ) ) {
                return $_SERVER[ $header ];
            }
        }

        // Check for a custom user agent header, if set by the user
        if ( ! empty( $this->custom_user_agent_header ) && ! empty( $_SERVER[ $this->custom_user_agent_header ] ) ) {
            return $_SERVER[ $this->custom_user_agent_header ];
        }

        return null; // User agent not found
    }
}
