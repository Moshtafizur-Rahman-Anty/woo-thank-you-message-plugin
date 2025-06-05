<?php
/**
 * Plugin Name: Custom Thank You Message for WooCommerce
 * Description: Displays a customizable thank-you message on the WooCommerce order confirmation page.
 * Version: 1.0
 * Author: Moshtafizur
 * Text Domain: custom-thank-you-for-woocommerce
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access for security
}

// Main plugin class
class WCTYM_ThankYouMessage {

    // Plugin option keys
    const OPTION_NAME          = 'wctym_custom_message';
    const OPTION_POSITION      = 'wctym_message_position';
    const OPTION_BG_COLOR      = 'wctym_bg_color';
    const OPTION_TEXT_COLOR    = 'wctym_text_color';
    const OPTION_BORDER_RADIUS = 'wctym_border_radius';

    // Constructor
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->hooks();
    }

    // Define paths for plugin directory and URL
    private function define_constants() {
        define( 'WCTYM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'WCTYM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }

    // Include required files
    private function includes() {
        require_once WCTYM_PLUGIN_DIR . 'includes/settings-page.php';
    }

    // Register all hooks and actions
    private function hooks() {
        $position = get_option( self::OPTION_POSITION, 'bottom_of_page' );

        // Choose display position for thank-you message
        switch ( $position ) {
            case 'top_of_page':
                add_action( 'woocommerce_before_thankyou', [ $this, 'display_thank_you_message' ], 5 );
                break;
            case 'above_order_table':
                add_action( 'woocommerce_order_details_before_order_table', [ $this, 'display_thank_you_message' ], 5 );
                break;
            case 'below_order_table':
                add_action( 'woocommerce_order_details_after_order_table', [ $this, 'display_thank_you_message' ], 5 );
                break;
            case 'bottom_of_page':
            default:
                add_action( 'woocommerce_thankyou', [ $this, 'display_thank_you_message' ], 5 );
                break;
        }

        // Load plugin stylesheet
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    // Enqueue front-end CSS
    public function enqueue_styles() {
        wp_enqueue_style( 'wctym-style', WCTYM_PLUGIN_URL . 'css/style.css', [], '1.0', 'all' );
    }

    // Display thank-you message on order confirmation page
    public function display_thank_you_message( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return; // Stop if order not found
        }

        $message = get_option( self::OPTION_NAME );

        if ( empty( $message ) ) {
            return; // Don't render empty message
        }

        // Replace placeholders with actual order/customer data
        $placeholders = [
            '[customer_name]' => $order->get_billing_first_name(),
            '[order_id]'      => $order->get_id(),
            '[billing_email]' => $order->get_billing_email(),
            '[total]'         => $order->get_formatted_order_total(),
        ];
        $final_message = strtr( $message, $placeholders );

        // Retrieve saved style settings
        $bg_color      = get_option( self::OPTION_BG_COLOR );
        $text_color    = get_option( self::OPTION_TEXT_COLOR );
        $border_radius = get_option( self::OPTION_BORDER_RADIUS );

        // Build inline styles with proper escaping
        $style = '';
        if ( $bg_color ) {
            $style .= 'background-color:' . esc_attr( $bg_color ) . ';';
        }
        if ( $text_color ) {
            $style .= 'color:' . esc_attr( $text_color ) . ';';
        }
        if ( $border_radius ) {
            if ( is_numeric( $border_radius ) ) {
                $border_radius .= 'px'; // Normalize numeric input
            }
            $style .= 'border-radius:' . esc_attr( $border_radius ) . ';';
        }

        // Output the styled thank-you message safely
        echo '<div class="wctym-thank-you"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>';
        echo wp_kses_post( wpautop( $final_message ) ); // Allow basic HTML and auto line breaks
        echo '</div>';
    }
}

// Initialize the plugin
new WCTYM_ThankYouMessage();

// Load textdomain for translations
add_action( 'plugins_loaded', 'wctym_load_textdomain' );
function wctym_load_textdomain() {
    load_plugin_textdomain( 'woo-thank-you-message', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
