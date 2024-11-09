<?php
// Ensure this file is only accessed through WordPress
if (!defined('ABSPATH')) exit;

$background_color = get_option('wp_bot_blocker_block_bg_color', '#f44336');
$font_color = get_option('wp_bot_blocker_block_font_color', '#ffffff');
$block_message = $message; // Use the message passed from block_request
$enable_recaptcha = get_option('wp_bot_blocker_enable_recaptcha_block', false);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: <?php echo esc_attr($background_color); ?>;
            color: <?php echo esc_attr($font_color); ?>;
            text-align: center;
        }
        .block-container {
            max-width: 400px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            background-color: <?php echo esc_attr($background_color); ?>;
        }
        .block-message {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .g-recaptcha {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="block-container">
        <div class="block-message">
            <?php echo esc_html($block_message); ?>
        </div>
        <?php if ($enable_recaptcha): ?>
          <form method="post" >
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr(get_option('wp_bot_blocker_recaptcha_site_key')); ?>"></div>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        
         <br>
            <input type="submit" value = "Proceed, I am human" />
        </form>
        <?php endif; ?>
        <script>
  // If reCAPTCHA is still loading, grecaptcha will be undefined.
  grecaptcha.ready(function(){
    grecaptcha.render("container", {
      sitekey: "<?php echo esc_attr(get_option('wp_bot_blocker_recaptcha_site_key')); ?>"
    });
  });
</script>
    </div>
</body>
</html>
