=== WP Bot Blocker ===
Contributors: Samuel Chukwu 
Donate link: https://yourwebsite.com/donate
Tags: bot protection, rate limiting, recaptcha, AbuseIPDB, honey pot, WordPress security
Requires at least: 6.0
Tested up to: 6.6
Stable tag: 2.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin to block bad bots, implement rate limiting, and protect your site with reCAPTCHA and Honey Pot integration.

== Description ==

WP Bot Blocker is a simple yet powerful plugin that helps you secure your WordPress site by blocking malicious bots, limiting request rates, and enabling bot detection with advanced detection APIs including reCAPTCHA, Honey Pot GeoIp, AbuseIPDB and similar services.
All API services is optional and helps to strengthen the detection system. By the default, the plugin detects the most basic common bots like AI Scrapers, Abusing Crwlers, Malicious Spiders and Spammers. 

WP Bot Blocker uses score of 1 to 10 to grade bots. 

Score 1 = Definitely not a bot.
Score 5 = More Likely a bot. 
Score 10 = Definitely a bot. 

Setting the score threshold in WP Bot Blocker Settings determines how the plugin generally threat a request based on the score, this does not apply to rules. Use Advanced Bot Blocker rules for more robust and specific detection  


WP Bot Blocker has inbuilt Advanced Block Rules which can be configured to block bot traffic according to needs. Rules allows you to basically set up a simple WAF based on simple structure:
**Rule Type ** -> **Condition** -> **Action**

* **Rule Type** Take action on a request based on IP, User Agent, Request URI, Country, Continent, 

* **Bot Blocking:** Identify and block suspicious bots based on User-Agent, rate-limiting, and Honey Pot data.
* **Rate Limiting:** Limit the number of requests per IP to prevent abuse.
* **reCAPTCHA & Honey Pot Integration:** Use reCAPTCHA to verify human visitors and Honey Pot to identify known malicious IPs.
* **Live Traffic Monitor:** Monitor traffic activity in real time and review blocked requests.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-bot-blocker` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to "Settings > WP Bot Blocker" to configure plugin settings.

== Frequently Asked Questions ==

= How do I enable reCAPTCHA and Honey Pot? =
You can enter your reCAPTCHA and Honey Pot API keys in the plugin settings.

= Can I adjust the rate limit threshold? =
Yes, the plugin provides an option to set a custom rate limit threshold and time window.

== Screenshots ==

1. Plugin settings page with reCAPTCHA and Honey Pot options.
2. Live traffic monitor showing real-time traffic data.
3. Block page displayed to blocked bots.

== Changelog ==

= 1.0.0 =
* Initial release of WP Bot Blocker.

== Upgrade Notice ==

= 1.0.0 =
* Initial release.
