=== WP Bot Blocker ===
Contributors: Samuel Chukwu 
Donate link: https://yourwebsite.com/donate
Tags: bot protection, rate limiting, recaptcha, AbuseIPDB, honey pot, WordPress security
Requires at least: 6.6
Tested up to: 6.6
Stable tag: 2.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin to block bad bots, implement rate limiting, and protect your site with reCAPTCHA and Honey Pot integration.

== Description ==

WP Bot Blocker is a FREE and Open Source solution to decisively deal with bots and malicious traffic. 

WP Bot Blocker is a simple yet powerful WordPress plugin that helps secure WordPress sites by blocking malicious bots, limiting request rates, and enabling bot detection.
Wp Bot Blocker works by intercepting all incoming requests to WordPress Fronted early enough and taking appropriate action by applying the Block Rules or intercepting the request based on the defined settings.
The plugin checks for:

>> Rate Limit. 

>> Bot Score. 

>> Block Rules 

The request is allowed to proceed if it passed.



WP Bot Blocker uses external API including Recaptcha and AbuseIPDB to strengthen the detection system. All API services is optional and helps to strengthen the bot detection. By the default, the plugin detects the most basic common bots like AI Scrapers, Abusing Crwlers, Malicious Spiders and Spammers. 

WP Bot Blocker uses score of 1 to 10 to grade bots. 

*Score 1* = Definitely not a bot.

*Score 5* = More Likely a bot. 

*Score 10* = Definitely a bot. 

Setting the score threshold in WP Bot Blocker Settings determines how the plugin generally threat a request based on the score, this does not apply to rules. Use Advanced Bot Blocker rules for more robust and specific detection  


WP Bot Blocker has inbuilt Advanced Block Rules which can be configured to block bot traffic according to needs. Rules allows you to basically set up a simple WAF based on simple structure:
**Rule Type ** -> **Condition** -> **Action**

* **Rule Type** Take action on a request based on IP, User Agent, Request URI, Country, Continent, Full Request URI, Is Known Bot, Bot Category and Query Strings.

* **Condition** Based on Rule Type, you can set condition value or pattern to match for the request. 
For example, setting "* AI Bot *" as the condition value for User Agent will block all requests with user agent that contains "AI Bot". 
This means that wildcards are accepted in the condition pattern to block. 

* **Action** WP Bot Blocker takes an action when the request match the condition value. Actions can be to "**Block**" the request or "**Redirect**" the request to some defined url. 

**Features**

* **Bot Blocking:** Identify and block suspicious bots based on User-Agent, rate-limiting, and Bot Score.
* **Rate Limiting:** Limit the number of requests per IP to prevent abuse.
* **API Integration:** Use reCAPTCHA to verify human visitors and other API to identify known malicious IPs.
* **Live Traffic Monitor:** Monitor traffic activity in real time and review blocked requests.
* **Block Rules: ** Take action on each single traffic and requests that comes to your site by either blocking or redirecting them based on IP, User Agent, Country, Requested URL, Continent, If It's Bot, Bot Category, Query Strings and more. 

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
= 2.0.0 =
* WP Bot Blocker goes public. 

== Upgrade Notice ==

= 2.0.1 =
* Integrated with github for automated and open source release. 

= 2.0.2 =
* Implemented Recaptcha V3.
* Enable or disable Recaptcha V3 by choice.
* Detect malicious traffic by score threshold.
* Cache Api calls for performance.