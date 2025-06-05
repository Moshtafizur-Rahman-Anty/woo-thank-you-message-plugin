<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access for security
}

class WCTYM_Settings_Page {

    public function __construct() {
        // Register admin settings menu
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );

        // Register settings fields
        add_action( 'admin_init', [ $this, 'register_settings' ] );

        // Enqueue WP color picker
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_color_picker' ] );
    }

    public function add_settings_page() {
        add_options_page(
            __( 'Thank You Message Settings', 'custom-thank-you-for-woocommerce' ),
            __( 'Thank You Message', 'custom-thank-you-for-woocommerce' ),
            'manage_options',
            'wctym-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    public function enqueue_color_picker( $hook_suffix ) {
        // Only load color picker script on our plugin settings page
        if ( $hook_suffix !== 'settings_page_wctym-settings' ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script(
            'wctym-color-picker',
            WCTYM_PLUGIN_URL . 'includes/color-picker.js',
            [ 'wp-color-picker' ],
            '1.0',
            true
        );
    }

    public function register_settings() {
        // Register the custom message setting with wp_kses_post sanitizer to allow safe HTML
        register_setting( 'wctym_settings_group', 'wctym_custom_message', [
            'sanitize_callback' => 'wp_kses_post',
        ]);

        // Register the message position setting with a custom sanitizer to allow only predefined values
        register_setting( 'wctym_settings_group', 'wctym_message_position', [
            'sanitize_callback' => function ( $input ) {
                $allowed = [ 'top_of_page', 'above_order_table', 'below_order_table', 'bottom_of_page' ];
                return in_array( $input, $allowed, true ) ? $input : 'bottom_of_page';
            },
        ]);

        // Register the background color setting with sanitize_hex_color to allow only valid HEX values
        register_setting( 'wctym_settings_group', 'wctym_bg_color', [
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        // Register the text color setting with sanitize_hex_color to allow only valid HEX values
        register_setting( 'wctym_settings_group', 'wctym_text_color', [
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        // Register the border radius setting with a custom callback that ensures it's a non-negative integer
        register_setting( 'wctym_settings_group', 'wctym_border_radius', [
            'sanitize_callback' => function ( $input ) {
                return is_numeric( $input ) ? absint( $input ) : '';
            },
        ]);

        // Create a section for the plugin settings with a title and optional description (null here)
        add_settings_section(
            'wctym_main_section',
            __( 'Customize Thank You Message', 'custom-thank-you-for-woocommerce' ), // Corrected text domain
            null,
            'wctym-settings'
        );

        // Add the field for the custom message textarea
        add_settings_field(
            'wctym_custom_message',
            __( 'Custom Message', 'custom-thank-you-for-woocommerce' ),
            [ $this, 'message_field_callback' ],
            'wctym-settings',
            'wctym_main_section'
        );

        // Add the dropdown selector field for message position
        add_settings_field(
            'wctym_message_position',
            __( 'Message Position', 'custom-thank-you-for-woocommerce' ),
            [ $this, 'position_field_callback' ],
            'wctym-settings',
            'wctym_main_section'
        );

        // Add the background color picker input
        add_settings_field(
            'wctym_bg_color',
            __( 'Background Color', 'custom-thank-you-for-woocommerce' ),
            [ $this, 'bg_color_field_callback' ],
            'wctym-settings',
            'wctym_main_section'
        );

        // Add the text color picker input
        add_settings_field(
            'wctym_text_color',
            __( 'Text Color', 'custom-thank-you-for-woocommerce' ),
            [ $this, 'text_color_field_callback' ],
            'wctym-settings',
            'wctym_main_section'
        );

        // Add the input field for border radius (numeric, in pixels)
        add_settings_field(
            'wctym_border_radius',
            __( 'Border Radius (px)', 'custom-thank-you-for-woocommerce' ),
            [ $this, 'border_radius_field_callback' ],
            'wctym-settings',
            'wctym_main_section'
        );
    }

    // Field: Custom Message
    public function message_field_callback() {
        $value = get_option( 'wctym_custom_message', '' );
        echo '<textarea name="wctym_custom_message" rows="5" cols="50">' . esc_textarea( $value ) . '</textarea>';
        echo '<p class="description">' . esc_html__( 'Use placeholders like [customer_name], [order_id], [billing_email], [total].', 'custom-thank-you-for-woocommerce' ) . '</p>';
    }

    // Field: Position Selector
    public function position_field_callback() {
        $value = get_option( 'wctym_message_position', 'bottom_of_page' );

        $positions = [
            'top_of_page'       => __( 'Top of Page', 'custom-thank-you-for-woocommerce' ),
            'above_order_table' => __( 'Above Order Table', 'custom-thank-you-for-woocommerce' ),
            'below_order_table' => __( 'Below Order Table', 'custom-thank-you-for-woocommerce' ),
            'bottom_of_page'    => __( 'Bottom of Page (Default)', 'custom-thank-you-for-woocommerce' ),
        ];

        echo '<select name="wctym_message_position">';
        foreach ( $positions as $key => $label ) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $key ),
                selected( $value, $key, false ),
                esc_html( $label )
            );
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__( 'Choose where the thank-you message should appear on the order received page.', 'custom-thank-you-for-woocommerce' ) . '</p>';
    }

    // Field: Background Color
    public function bg_color_field_callback() {
        $value = get_option( 'wctym_bg_color', '' );
        echo '<input type="text" name="wctym_bg_color" value="' . esc_attr( $value ) . '" class="wctym-color-field" />';
        echo '<p class="description">' . esc_html__( 'Leave empty to use default background style from plugin.', 'custom-thank-you-for-woocommerce' ) . '</p>';
    }

    // Field: Text Color
    public function text_color_field_callback() {
        $value = get_option( 'wctym_text_color', '' );
        echo '<input type="text" name="wctym_text_color" value="' . esc_attr( $value ) . '" class="wctym-color-field" />';
        echo '<p class="description">' . esc_html__( 'Leave empty to use default text color from plugin CSS.', 'custom-thank-you-for-woocommerce' ) . '</p>';
    }

    // Field: Border Radius
    public function border_radius_field_callback() {
        $value = get_option( 'wctym_border_radius', '' );
        echo '<input type="number" name="wctym_border_radius" value="' . esc_attr( $value ) . '" placeholder="5" />';
        echo '<p class="description">' . esc_html__( 'Optional border radius in pixels. Leave blank for default.', 'custom-thank-you-for-woocommerce' ) . '</p>';
    }

    // Render the full settings page
    public function render_settings_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'WooCommerce Thank You Message', 'custom-thank-you-for-woocommerce' ) . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields( 'wctym_settings_group' );
        do_settings_sections( 'wctym-settings' );
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

new WCTYM_Settings_Page();
