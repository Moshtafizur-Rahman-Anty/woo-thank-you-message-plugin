<?php
/**
 * Plugin Name: WooCommerce Thank You Message
 * Description: Displays a customizable thank-you message on the WooCommerce order confirmation page.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: wctym
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Main plugin class
class WCTYM_ThankYouMessage {

    const OPTION_NAME = 'wctym_custom_message';
    const OPTION_POSITION = 'wctym_message_position';


    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->hooks();
    }

    private function define_constants() {
        define( 'WCTYM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'WCTYM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }

    private function includes() {
        require_once WCTYM_PLUGIN_DIR . 'includes/settings-page.php';
    }

    private function hooks() {
$position = get_option( self::OPTION_POSITION, 'bottom_of_page' );

switch ( $position ) {
    case 'top_of_page':
        add_action( 'woocommerce_before_main_content', array( $this, 'display_thank_you_message' ), 5 );
        break;
    case 'above_order_table':
        add_action( 'woocommerce_order_details_before_order_table', array( $this, 'display_thank_you_message' ), 5 );
        break;
    case 'below_order_table':
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'display_thank_you_message' ), 5 );
        break;
    case 'bottom_of_page':
    default:
        add_action( 'woocommerce_thankyou', array( $this, 'display_thank_you_message' ), 5 );
        break;
}
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
    }

    public function enqueue_styles() {
        wp_enqueue_style( 'wctym-style', WCTYM_PLUGIN_URL . 'css/style.css' );
    }

    public function display_thank_you_message( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) return;

        $message = get_option( self::OPTION_NAME );

        if ( empty( $message ) ) return;

        // Replace placeholders
        $placeholders = array(
            '[customer_name]' => $order->get_billing_first_name(),
            '[order_id]'      => $order->get_id()
        );
        $final_message = strtr( $message, $placeholders );

        echo '<div class="wctym-thank-you">';
        echo wpautop( esc_html( $final_message ) );
        echo '</div>';
    }
}

// Initialize plugin
new WCTYM_ThankYouMessage();
