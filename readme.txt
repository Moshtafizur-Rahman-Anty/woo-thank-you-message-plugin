=== WooCommerce Thank You Message ===
Contributors: Moshtafizur  
Tags: woocommerce, thank you page, order confirmation, message, customize  
Requires at least: 5.0  
Tested up to: 6.5  
Requires PHP: 7.2  
Stable tag: 1.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  
Plugin URI: https://github.com/Moshtafizur-Rahman-Anty/woo-thank-you-message-plugin/

Add a custom, personalized message to your WooCommerce Thank You page using dynamic placeholders like [customer_name], [order_id], [billing_email], and [total].

== Description ==

Display a custom thank-you message to your WooCommerce customers after they place an order. This plugin allows store owners to personalize the confirmation page with dynamic content such as the customer's name, email, order ID, and total price.

Use placeholders in your message:
- `[customer_name]` — Replaced with the customer's billing first name
- `[order_id]` — Replaced with the WooCommerce order ID
- `[billing_email]` — Replaced with the customer's billing email
- `[total]` — Replaced with the formatted order total

**This plugin is lightweight, translation-ready, and built with clean, OOP PHP.**

== Features ==

* Display a custom thank-you message on the WooCommerce order confirmation page
* Use dynamic placeholders like [customer_name], [order_id], [billing_email], [total]
* Choose where the message appears: top, above/below order table, or bottom of page
* Customize background color, text color, and border radius from the settings
* Lightweight, clean code built using OOP principles
* Translation-ready with `.pot` file included
* GitHub Repository: [Woo Thank You Message Plugin](https://github.com/Moshtafizur-Rahman-Anty/woo-thank-you-message-plugin/)

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/woo-thank-you-message`
2. Activate the plugin through the ‘Plugins’ menu in WordPress
3. Go to **Settings > Thank You Message**
4. Enter your custom message and choose placement
5. Save your settings — Done!

== Frequently Asked Questions ==

= Can I use HTML in the message? =  
Currently, the message supports only safe tags like `<strong>`, `<em>`, and `<a>`. Full HTML support may be added in future updates.

= Can I show different messages based on product or category? =  
Not at this time — but this may be introduced in future versions.

== Screenshots ==

1. Admin settings screen for customizing message and style  
2. Thank-you message shown on the WooCommerce order confirmation page

== Changelog ==

= 1.0 =
* Initial release with dynamic message support and flexible styling options

== Upgrade Notice ==

= 1.0 =
Initial version with support for custom messages and placeholders on the WooCommerce Thank You page.
