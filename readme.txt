=== WooCommerce Thank You Message ===
Contributors: yourusername
Tags: woocommerce, thank you page, order confirmation, message, customize
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 7.2
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a custom, personalized message to your WooCommerce Thank You page using placeholders like [customer_name] and [order_id].

== Description ==

Display a custom thank-you message to your WooCommerce customers after they place an order. This plugin allows store owners to personalize the confirmation page with dynamic content such as customer name or order ID.

Use placeholders in your message:
- `[customer_name]` — Replaced with the billing first name
- `[order_id]` — Replaced with the WooCommerce order ID

== Features ==

* Customize the message shown on the WooCommerce Thank You page
* Dynamic placeholders like [customer_name] and [order_id]
* Lightweight and written with clean, OOP PHP
* Easy admin settings page under “Settings > Thank You Message”
* Add styling with a dedicated CSS file

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/woo-thank-you-message`
2. Activate the plugin through the ‘Plugins’ menu in WordPress
3. Go to **Settings > Thank You Message**
4. Enter your message and save
5. Done!

== Frequently Asked Questions ==

= Can I add HTML to the message? =  
Currently, the message is plain text for safety. Future versions may support safe HTML tags.

= Can I add different messages based on the product or category? =  
Not yet — but this feature may be added in future versions.

== Screenshots ==

1. Admin settings screen
2. Thank-you message on WooCommerce order confirmation page

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
Initial version with custom message support for WooCommerce Thank You page.
