<div class="wrap">
    <h1><?php echo esc_html__('Live Traffic Monitor', 'wp-bot-blocker'); ?></h1>

    <!-- Section for Rate-Limited IPs -->
    <h2><?php echo esc_html__('Rate-Limited IPs', 'wp-bot-blocker'); ?></h2>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php echo esc_html__('ID', 'wp-bot-blocker'); ?></th>
                <th><?php echo esc_html__('IP Address', 'wp-bot-blocker'); ?></th>
                <th><?php echo esc_html__('User-Agent', 'wp-bot-blocker'); ?></th>
                <th><?php echo esc_html__('Blocked Time', 'wp-bot-blocker'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rate_limited_logs)) : ?>
                <?php foreach ($rate_limited_logs as $log) : ?>
                    <tr>
                        <td><?php echo esc_html($log->id); ?></td>
                        <td><?php echo esc_html($log->ip_address); ?></td>
                        <td><?php echo esc_html($log->user_agent); ?></td>
                        <td><?php echo esc_html($log->blocked_time); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4"><?php echo esc_html__('No rate-limited IPs found.', 'wp-bot-blocker'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Section for All Traffic Logs -->
    <h2><?php echo esc_html__('All Traffic Logs', 'wp-bot-blocker'); ?></h2>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php echo esc_html__('ID', 'wp-bot-blocker'); ?></th>
                <th><?php echo esc_html__('IP Address', 'wp-bot-blocker'); ?></th>
                <th><?php echo esc_html__('User-Agent', 'wp-bot-blocker'); ?></th>
                <th><?php echo esc_html__('Page Visited', 'wp-bot-blocker'); ?></th>
                <th><?php echo esc_html__('Visit Time', 'wp-bot-blocker'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($all_logs)) : ?>
                <?php foreach ($all_logs as $log) : ?>
                    <tr>
                        <td><?php echo esc_html($log->id); ?></td>
                        <td><?php echo esc_html($log->ip_address); ?></td>
                        <td><?php echo esc_html($log->user_agent); ?></td>
                        <td><?php echo esc_html($log->page_visited); ?></td>
                        <td><?php echo esc_html($log->visit_time); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php echo esc_html__('No traffic logs found.', 'wp-bot-blocker'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
