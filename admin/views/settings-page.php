<div class="wrap wp-bot-blocker-settings"> 
    <h1><?php echo esc_html__('Bot Blocker Settings', 'wp-bot-blocker'); ?></h1>
    
  <div class="wpbb-tabs">
        <button class="wpbb-tab-link active" onclick="openTab(event, 'general')">General Settings</button>
        <button class="wpbb-tab-link" onclick="openTab(event, 'api')">API Settings</button>
        <button class="wpbb-tab-link" onclick="openTab(event, 'block-rules')">Block Rules</button>
        <button class="wpbb-tab-link" onclick="openTab(event, 'rate-limit')">Rate Limit</button>
        <!-- Add more tabs as needed -->
    </div>  
    
    
    <div id="general" class="wpbb-tab-content active">
        <h2>General Settings</h2>
        <form method="post" action="options.php">
            <?php
                settings_fields('wp_bot_blocker_settings');
                do_settings_sections('wp_bot_blocker_general');
                submit_button();
            ?>
        </form>
    </div>
    
    
    <div id="api" class="wpbb-tab-content">
        <h2>API Settings</h2>
        <form method="post" action="options.php">
            <?php
                settings_fields('wp_bot_blocker_api');
                do_settings_sections('wp_bot_blocker_api');
                submit_button();
            ?>
        </form>
    </div>
    
    
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp-bot-blocker-settings');
        do_settings_sections('wp-bot-blocker-settings');
        ?>
        
        <!-- reCAPTCHA Settings -->
        <h2><?php echo esc_html__('reCAPTCHA Settings', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('reCAPTCHA Site Key', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_recaptcha_site_key" value="<?php echo esc_attr(get_option('wp_bot_blocker_recaptcha_site_key')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('reCAPTCHA Secret Key', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_recaptcha_secret_key" value="<?php echo esc_attr(get_option('wp_bot_blocker_recaptcha_secret_key')); ?>" /></td>
            </tr>
        </table>

        <!-- Honey Pot Settings -->
        <h2><?php echo esc_html__('Honey Pot Settings', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Honey Pot API Key', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_honeypot_api_key" value="<?php echo esc_attr(get_option('wp_bot_blocker_honeypot_api_key')); ?>" /></td>
            </tr>
        </table>

        <!-- Bot Detection Settings -->
        <h2><?php echo esc_html__('Bot Detection Settings', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Exclude Bots (User-Agent)', 'wp-bot-blocker'); ?></th>
                <td><input type="text" name="wp_bot_blocker_excluded_bots" value="<?php echo esc_attr(get_option('wp_bot_blocker_excluded_bots', 'Googlebot, Bingbot')); ?>" placeholder="e.g., Googlebot, Bingbot" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Bot Score Threshold', 'wp-bot-blocker'); ?></th>
                <td><input type="number" name="wp_bot_blocker_score_threshold" value="<?php echo esc_attr(get_option('wp_bot_blocker_score_threshold', 5)); ?>" min="1" max="10" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Enable Honey Pot Check', 'wp-bot-blocker'); ?></th>
                <td><input type="checkbox" name="wp_bot_blocker_enable_honeypot" value="1" <?php checked(get_option('wp_bot_blocker_enable_honeypot'), 1); ?> /></td>
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

        <!-- Rate Limiting Settings -->
        <h2><?php echo esc_html__('Rate Limiting Settings', 'wp-bot-blocker'); ?></h2>
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
        </table>

        <!-- Live Traffic Monitor Settings -->
        <h2><?php echo esc_html__('Live Traffic Monitor Settings', 'wp-bot-blocker'); ?></h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Enable Traffic Monitor', 'wp-bot-blocker'); ?></th>
                <td><input type="checkbox" name="wp_bot_blocker_enable_traffic_monitor" value="1" <?php checked(get_option('wp_bot_blocker_enable_traffic_monitor'), 1); ?> /></td>
            </tr>
        </table>

        <!-- Block Page Customization -->
        <h2><?php echo esc_html__('Block Page Customization', 'wp-bot-blocker'); ?></h2>
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
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Custom Block Message for Rate Limit', 'wp-bot-blocker'); ?></th>
                <td><textarea name="wp_bot_blocker_rate_limit_message" rows="3"><?php echo esc_textarea(get_option('wp_bot_blocker_rate_limit_message', 'Too many requests. Please try again later.')); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Enable reCAPTCHA on Block Page', 'wp-bot-blocker'); ?></th>
                <td><input type="checkbox" name="wp_bot_blocker_enable_recaptcha_block" value="1" <?php checked(get_option('wp_bot_blocker_enable_recaptcha_block'), 1); ?> /></td>
            </tr>
        </table>
        
        <!-- Log Management Settings -->
<h2><?php echo esc_html__('Log Management', 'wp-bot-blocker'); ?></h2>
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
    </form>
    
        <p><?php echo esc_html__('Manual Log Cleanup', 'wp-bot-blocker'); ?></p>
        
            
            <form method="post" >
                <?php wp_nonce_field('wp_bot_blocker_clear_logs'); ?>
                <input type="hidden" name="wp_bot_blocker_clear_logs" value="1" />
                <button type="submit" class="button button-secondary"><?php echo esc_html__('Clear All Logs Now', 'wp-bot-blocker'); ?></button>
            </form>

    
</div>

