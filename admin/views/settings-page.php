<div class="wrap wp-bot-blocker-settings"> 
    <h1><?php echo esc_html__('Settings', 'wp-bot-blocker'); ?></h1>
    
  <div class="wpbb-tabs">
        <button class="wpbb-tab-link active" onclick="openTab(event, 'general')">General Settings</button>
        <button class="wpbb-tab-link" onclick="openTab(event, 'api')">API</button>
        <button class="wpbb-tab-link" onclick="openTab(event, 'rate-limit')">Rate Limit</button>
        <button class="wpbb-tab-link" onclick="openTab(event, 'tools')">Tools</button>
        
        <!-- Add more tabs as needed -->
    </div>  
    
    
     <form method="post" action="options.php">
         <?php
        settings_fields('wp-bot-blocker-settings');
        do_settings_sections('wp-bot-blocker-settings');
        ?>
    
    
    <div id="general" class="wpbb-tab-content active">
        
   


        <!-- Bot Detection Settings -->
        <h2><?php echo esc_html__('Bot Detection', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Exclude Bots (User-Agent)', 'wp-bot-blocker'); ?></th>
                <td>
                  <textarea rows="4" cols="50" name="wp_bot_blocker_excluded_bots" placeholder="e.g., Googlebot, Bingbot"  ><?php echo esc_attr(get_option('wp_bot_blocker_excluded_bots', 'Googlebot, Bingbot')); ?></textarea> 
                    <p class="description">Separated with Comma ( , ) </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Bot Score Threshold', 'wp-bot-blocker'); ?></th>
                <td>
                  <input type="number" name="wp_bot_blocker_score_threshold" value="<?php echo esc_attr(get_option('wp_bot_blocker_score_threshold', 5)); ?>" min="1" max="10" required="true" />
                    <p class="description">Score from 1 to 10.
                    1 is likely not a bot.
                    10 is definitely a bad bot.
                    </p>
                  </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Bot Types to Monitor', 'wp-bot-blocker'); ?></th>
                <td>
                    <label><input type="checkbox" name="wp_bot_blocker_monitor_crawlers" value="1" <?php checked(get_option('wp_bot_blocker_monitor_crawlers'), 1); ?> /> <?php echo esc_html__('Crawlers', 'wp-bot-blocker'); ?></label><br>
                    <label><input type="checkbox" name="wp_bot_blocker_monitor_scrapers" value="1" <?php checked(get_option('wp_bot_blocker_monitor_scrapers'), 1); ?> /> <?php echo esc_html__('Scrapers', 'wp-bot-blocker'); ?></label><br>
                    <label><input type="checkbox" name="wp_bot_blocker_monitor_spammers" value="1" <?php checked(get_option('wp_bot_blocker_monitor_spammers'), 1); ?> /> <?php echo esc_html__('Spammers', 'wp-bot-blocker'); ?></label>
                </td>
            </tr>
        </table>

       <br>

        <!-- Live Traffic Monitor Settings -->
        <h2><?php echo esc_html__('Live Traffic Monitor', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Enable Traffic Monitor', 'wp-bot-blocker'); ?></th>
                <td><input type="checkbox" name="wp_bot_blocker_enable_traffic_monitor" value="1" <?php checked(get_option('wp_bot_blocker_enable_traffic_monitor'), 1); ?> />
                  <p class="description">Traffic monitor logs every single visit to this WordPress site.
                  For performance sake, enable this only while debugging, especially for high traffic sites.</p>
                </td>
            </tr>
        </table>
        
        <br>

        <!-- Block Page Customization -->
        <h2><?php echo esc_html__('Customize Block Page', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Block Page Background Color', 'wp-bot-blocker'); ?></th>
                <td><input type="color" name="wp_bot_blocker_block_bg_color" value="<?php echo esc_attr(get_option('wp_bot_blocker_block_bg_color', '#f44336')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Block Page Font Color', 'wp-bot-blocker'); ?></th>
                <td><input type="color" name="wp_bot_blocker_block_font_color" value="<?php echo esc_attr(get_option('wp_bot_blocker_block_font_color', '#ffffff')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Custom Block Message for Bot Detection', 'wp-bot-blocker'); ?></th>
                <td><textarea name="wp_bot_blocker_bot_detection_message" rows="3"><?php echo esc_textarea(get_option('wp_bot_blocker_bot_detection_message', 'Access Denied. Please verify you are human.')); ?></textarea></td>
            </tr>


        </table>
           <br>

        <!-- Log Management Settings -->
<h2><?php echo esc_html__('Logs', 'wp-bot-blocker'); ?></h2>
<table class="form-table">
    <tr valign="top">
        <th scope="row"><?php echo esc_html__('Log Retention Period', 'wp-bot-blocker'); ?></th>
        <td>
            <select name="wp_bot_blocker_log_retention">
                <option value="1" <?php selected(get_option('wp_bot_blocker_log_retention'), 1); ?>><?php echo esc_html__('1 Month', 'wp-bot-blocker'); ?></option>
                <option value="3" <?php selected(get_option('wp_bot_blocker_log_retention'), 3); ?>><?php echo esc_html__('3 Months', 'wp-bot-blocker'); ?></option>
                <option value="6" <?php selected(get_option('wp_bot_blocker_log_retention'), 6); ?>><?php echo esc_html__('6 Months', 'wp-bot-blocker'); ?></option>
                <option value="12" <?php selected(get_option('wp_bot_blocker_log_retention'), 12); ?>><?php echo esc_html__('1 Year', 'wp-bot-blocker'); ?></option>
            </select>
        </td>
    </tr>

</table>

        <?php submit_button(); ?>
    
            
            

    
</div>

    <div id="api" class="wpbb-tab-content">
        
       <!-- reCAPTCHA v2 Settings -->
        <h2><?php echo esc_html__('reCAPTCHA v2', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
              <tr valign="top">
                <th scope="row"><?php echo esc_html__('Enable reCAPTCHA Challenge on Block Page', 'wp-bot-blocker'); ?></th>
                <td><input type="checkbox" name="wp_bot_blocker_enable_recaptcha_block" value="1" <?php checked(get_option('wp_bot_blocker_enable_recaptcha_block'), 1); ?> /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('reCAPTCHA v2 Site Key', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_recaptcha_site_key" value="<?php echo esc_attr(get_option('wp_bot_blocker_recaptcha_site_key')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('reCAPTCHA v2 Secret Key', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_recaptcha_secret_key" value="<?php echo esc_attr(get_option('wp_bot_blocker_recaptcha_secret_key')); ?>" /></td>
            </tr>
        </table>
        <br>
   
   <!-- reCAPTCHA v3 Settings -->
        <h2><?php echo esc_html__('reCAPTCHA v3', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
              <tr valign="top">
                <th scope="row"><?php echo esc_html__('Enable reCAPTCHA v3', 'wp-bot-blocker'); ?></th>
                <td><input type="checkbox" name="wp_bot_blocker_enable_recaptchav3" value="1" <?php checked(get_option('wp_bot_blocker_enable_recaptchav3'), 1); ?> /></td>
            </tr>
            <tr>
            <th scope="row"><label for="wp_bot_blocker_recaptcha_threshold">reCAPTCHA Threshold Score</label></th>
            <td>
            <input type="number" step="0.1" min="0" max="1" name="wp_bot_blocker_recaptcha_threshold" id="wp_bot_blocker_recaptcha_threshold" value="<?php echo esc_attr(get_option('wp_bot_blocker_recaptcha_threshold', 0.5)); ?>" class="small-text" />
             <p class="description">Set the score threshold between 0.0 and 1.0. Users with scores below this value will be considered bots.</p>
           </td>
           </tr>

            <tr valign="top">
                <th scope="row"><?php echo esc_html__('reCAPTCHA v3 Site Key', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_recaptchav3_site_key" value="<?php echo esc_attr(get_option('wp_bot_blocker_recaptchav3_site_key')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('reCAPTCHA v3 Secret Key', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_recaptchav3_secret_key" value="<?php echo esc_attr(get_option('wp_bot_blocker_recaptchav3_secret_key')); ?>" /></td>
            </tr>
        </table>
        <br>
        <!-- Honey Pot Settings -->
        <h2><?php echo esc_html__('Honey Pot', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
                <tr valign="top">
                <th scope="row"><?php echo esc_html__('Enable Honey Pot Check', 'wp-bot-blocker'); ?></th>
                <td><input type="checkbox" name="wp_bot_blocker_enable_honeypot" value="1" <?php checked(get_option('wp_bot_blocker_enable_honeypot'), 1); ?> /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Honey Pot API Key', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_honeypot_api_key" value="<?php echo esc_attr(get_option('wp_bot_blocker_honeypot_api_key')); ?>" /></td>
            </tr>
            
        </table>
        <br>
        <!-- AbuseIPDB Settings -->
        <h2><?php echo esc_html__('AbuseIPDB', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
                <tr valign="top">
                <th scope="row"><?php echo esc_html__('Enable AbuseIPDB', 'wp-bot-blocker'); ?></th>
                <td><input type="checkbox" name="wp_bot_blocker_enable_abuseipdb" value="1" <?php checked(get_option('wp_bot_blocker_enable_abuseipdb'), 1); ?> /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('AbuseIPDB API Key', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_abuseipdb_api_key" value="<?php echo esc_attr(get_option('wp_bot_blocker_abuseipdb_api_key')); ?>" /></td>
            </tr>
            
        </table>
           <?php
           submit_button();
            ?>
        
    </div>
    
    <!-- RATE LIMIT TAB -->
    <div id="rate-limit" class="wpbb-tab-content">
        <h2>Rate Limit</h2>
        
            
    <!-- Rate Limit Settings -->
        
        <table class="form-table">
         
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Rate Limit Threshold (Requests)', 'wp-bot-blocker'); ?></th>
                <td><input type="number" name="wp_bot_blocker_rate_limit_threshold" value="<?php echo esc_attr(get_option('wp_bot_blocker_rate_limit_threshold', 10)); ?>" min="1" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Rate Limit Window (Seconds)', 'wp-bot-blocker'); ?></th>
                <td><input type="number" name="wp_bot_blocker_rate_limit_window" value="<?php echo esc_attr(get_option('wp_bot_blocker_rate_limit_window', 60)); ?>" min="1" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Block Duration (Seconds)', 'wp-bot-blocker'); ?></th>
                <td><input type="number" name="wp_bot_blocker_rate_limit_block_duration" value="<?php echo esc_attr(get_option('wp_bot_blocker_rate_limit_block_duration', 300)); ?>" min="1" /></td>
            </tr>  
                       <tr valign="top">
                <th scope="row"><?php echo esc_html__('Custom Block Message for Rate Limit', 'wp-bot-blocker'); ?></th>
                <td><textarea name="wp_bot_blocker_rate_limit_message" rows="3"><?php echo esc_textarea(get_option('wp_bot_blocker_rate_limit_message', 'Too many requests. Please try again later.')); ?></textarea></td>
            </tr>

        </table>
           <?php
           submit_button();
            ?>
        
    </div>
   </form>
    <!-- TOOLS TAB -->
    <div id="tools" class="wpbb-tab-content">
      <p><?php echo esc_html__('Manual Log Cleanup', 'wp-bot-blocker'); ?></p>
        
            
            <form method="post" >
                <?php wp_nonce_field('wp_bot_blocker_clear_logs'); ?>
                <input type="hidden" name="wp_bot_blocker_clear_logs" value="1" />
                <button type="submit" class="button button-secondary"><?php echo esc_html__('Clear All Logs Now', 'wp-bot-blocker'); ?></button>
            </form>

    </div>
        
 </div>


