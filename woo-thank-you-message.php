<?php
/**
 * Plugin Name: WooCommerce Thank You Message
 * Description: Displays a customizable thank-you message on the WooCommerce order confirmation page.
 * Version: 1.0
 * Author: Moshtafizur
 * Text Domain: wctym
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly for security
}

// Main plugin class
class WCTYM_ThankYouMessage {

    // Option keys for storing user settings in the database
    const OPTION_NAME          = 'wctym_custom_message';
    const OPTION_POSITION      = 'wctym_message_position';
    const OPTION_BG_COLOR      = 'wctym_bg_color';
    const OPTION_TEXT_COLOR    = 'wctym_text_color';
    const OPTION_BORDER_RADIUS = 'wctym_border_radius';

    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->hooks();
    }

    // Define plugin paths as constants for easy access
    private function define_constants() {
        define( 'WCTYM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'WCTYM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }

    // Load required files
    private function includes() {
        require_once WCTYM_PLUGIN_DIR . 'includes/settings-page.php';
    }

    // Register necessary hooks based on settings
    private function hooks() {
        $position = get_option( self::OPTION_POSITION, 'bottom_of_page' );

        // Hook placement based on admin setting
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

        // Load styles on the frontend
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );

    }

    // Load external plugin stylesheet
    public function enqueue_styles() {
        wp_enqueue_style( 'wctym-style', WCTYM_PLUGIN_URL . 'css/style.css', [], '1.0', 'all' );
    }

    // Output the thank you message
    public function display_thank_you_message( $order_id ) {
        $order = wc_get_order( $order_id );

        // Sanity check
        if ( ! $order ) {
            return;
        }

        $message = get_option( self::OPTION_NAME );

        // Don't render if message is empty
        if ( empty( $message ) ) {
            return;
        }

        // Replace placeholders with dynamic order data
        $placeholders = [
            '[customer_name]' => $order->get_billing_first_name(),
            '[order_id]'      => $order->get_id(),
            '[billing_email]' => $order->get_billing_email(),
            '[total]'         => $order->get_formatted_order_total(),
        ];
        $final_message = strtr( $message, $placeholders );

        // Get style values from settings
        $bg_color      = get_option( self::OPTION_BG_COLOR );
        $text_color    = get_option( self::OPTION_TEXT_COLOR );
        $border_radius = get_option( self::OPTION_BORDER_RADIUS );

        // Build inline style string safely
        $style = '';
        if ( $bg_color ) {
            $style .= 'background-color:' . esc_attr( $bg_color ) . ';';
        }
        if ( $text_color ) {
            $style .= 'color:' . esc_attr( $text_color ) . ';';
        }
        if ( $border_radius ) {
            // Allow "10" or "10px" — normalize to px if numeric
            if ( is_numeric( $border_radius ) ) {
                $border_radius .= 'px';
            }
            $style .= 'border-radius:' . esc_attr( $border_radius ) . ';';
        }

        // Output the final styled message
        echo '<div class="wctym-thank-you"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>';
        echo wpautop( wp_kses_post( $final_message ) ); // wp_kses_post allows safe tags like <strong>, <em>, <a>, etc.
        echo '</div>';
    }
}

// Initialize plugin
new WCTYM_ThankYouMessage();


// Load plugin textdomain for translations
add_action( 'plugins_loaded', 'wctym_load_textdomain' );

function wctym_load_textdomain() {
    load_plugin_textdomain( 'wctym', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
