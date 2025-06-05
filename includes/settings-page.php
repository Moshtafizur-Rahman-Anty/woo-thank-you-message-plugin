<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WCTYM_Settings_Page {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_settings_page() {
        add_options_page(
            'Thank You Message Settings',
            'Thank You Message',
            'manage_options',
            'wctym-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'wctym_settings_group', 'wctym_custom_message' );

        add_settings_section(
            'wctym_main_section',
            'Customize Thank You Message',
            null,
            'wctym-settings'
        );

        add_settings_field(
            'wctym_custom_message',
            'Custom Message',
            array( $this, 'message_field_callback' ),
            'wctym-settings',
            'wctym_main_section'
        );
    }

    public function message_field_callback() {
        $value = get_option( 'wctym_custom_message', '' );
        echo '<textarea name="wctym_custom_message" rows="5" cols="50">' . esc_textarea( $value ) . '</textarea>';
        echo '<p class="description">Use placeholders like [customer_name] and [order_id].</p>';
    }

    public function render_settings_page() {
        echo '<div class="wrap">';
        echo '<h1>WooCommerce Thank You Message</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields( 'wctym_settings_group' );
        do_settings_sections( 'wctym-settings' );
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

new WCTYM_Settings_Page();
