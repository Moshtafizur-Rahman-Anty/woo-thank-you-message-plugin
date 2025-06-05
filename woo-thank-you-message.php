<?php
/**
 * Plugin Name: WooCommerce Thank You Message
 * Description: Displays a customizable thank-you message on the WooCommerce order confirmation page.
 * Version: 1.0
 * Author: Moshtafizur
 * Text Domain: wctym
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Main plugin class
class WCTYM_ThankYouMessage {

    const OPTION_NAME = 'wctym_custom_message';
    const OPTION_POSITION = 'wctym_message_position';
    const OPTION_BG_COLOR = 'wctym_bg_color';
    const OPTION_TEXT_COLOR = 'wctym_text_color';
    const OPTION_BORDER_RADIUS = 'wctym_border_radius';



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
    add_action( 'woocommerce_before_thankyou', array( $this, 'display_thank_you_message' ), 5 );
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
    '[order_id]'      => $order->get_id(),
    '[billing_email]' => $order->get_billing_email(),
    '[total]'         => $order->get_formatted_order_total()
);
        $final_message = strtr( $message, $placeholders );


    // Style overrides (optional)
    $bg_color       = get_option( self::OPTION_BG_COLOR );
    $text_color     = get_option( self::OPTION_TEXT_COLOR );
    $border_radius  = get_option( self::OPTION_BORDER_RADIUS );

    $style = '';
    if ( $bg_color ) {
        $style .= 'background-color:' . esc_attr( $bg_color ) . ';';
    }
    if ( $text_color ) {
        $style .= 'color:' . esc_attr( $text_color ) . ';';
    }
    if ( $border_radius ) {
        if ( is_numeric( $border_radius ) ) {
        $border_radius .= 'px'; // convert 10 → 10px
    }
        $style .= 'border-radius:' . esc_attr( $border_radius ) . ';';
    }

    echo '<div class="wctym-thank-you"' . ( $style ? ' style="' . $style . '"' : '' ) . '>';
    echo wpautop( wp_kses_post( $final_message ) );
    echo '</div>';
}




}

// Initialize plugin
new WCTYM_ThankYouMessage();
