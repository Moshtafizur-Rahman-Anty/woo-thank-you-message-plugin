<?php

if (! defined('ABSPATH')) {
    exit;
}

class WCTYM_Settings_Page
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );

    }

    public function add_settings_page()
    {
        add_options_page(
            'Thank You Message Settings',
            'Thank You Message',
            'manage_options',
            'wctym-settings',
            [$this, 'render_settings_page']
        );
    }

    public function enqueue_color_picker( $hook_suffix ) {
    // Only load on your plugin settings page
    if ( $hook_suffix !== 'settings_page_wctym-settings' ) return;

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script(
        'wctym-color-picker',
        WCTYM_PLUGIN_URL . 'includes/color-picker.js',
        array( 'wp-color-picker' ),
        false,
        true
    );
}


    public function register_settings()
    {
        register_setting('wctym_settings_group', 'wctym_custom_message');
        register_setting('wctym_settings_group', 'wctym_message_position');

        register_setting('wctym_settings_group', 'wctym_bg_color');
        register_setting('wctym_settings_group', 'wctym_text_color');
        register_setting('wctym_settings_group', 'wctym_border_radius');

        add_settings_section(
            'wctym_main_section',
            'Customize Thank You Message',
            null,
            'wctym-settings'
        );

        add_settings_field(
            'wctym_custom_message',
            'Custom Message',
            [$this, 'message_field_callback'],
            'wctym-settings',
            'wctym_main_section'
        );

        add_settings_field(
            'wctym_message_position',
            'Message Position',
            [$this, 'position_field_callback'],
            'wctym-settings',
            'wctym_main_section'
        );

        add_settings_field(
    'wctym_bg_color',
    'Background Color',
    array( $this, 'bg_color_field_callback' ),
    'wctym-settings',
    'wctym_main_section'
);

add_settings_field(
    'wctym_text_color',
    'Text Color',
    array( $this, 'text_color_field_callback' ),
    'wctym-settings',
    'wctym_main_section'
);

add_settings_field(
    'wctym_border_radius',
    'Border Radius (px)',
    array( $this, 'border_radius_field_callback' ),
    'wctym-settings',
    'wctym_main_section'
);



    }

    public function message_field_callback()
    {
        $value = get_option('wctym_custom_message', '');
        echo '<textarea name="wctym_custom_message" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">Use placeholders like [customer_name], [order_id], [billing_email], [total].</p>';
    }

    public function position_field_callback()
    {
        $value = get_option('wctym_message_position', 'bottom_of_page');

        $positions = [
            'top_of_page'       => 'Top of Page',
            'above_order_table' => 'Above Order Table',
            'below_order_table' => 'Below Order Table',
            'bottom_of_page'    => 'Bottom of Page (Default)',
        ];

        echo '<select name="wctym_message_position">';
        foreach ($positions as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
        echo '<p class="description">Choose where the thank-you message should appear on the order received page.</p>';
    }

    public function render_settings_page()
    {
        echo '<div class="wrap">';
        echo '<h1>WooCommerce Thank You Message</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('wctym_settings_group');
        do_settings_sections('wctym-settings');
        submit_button();
        echo '</form>';
        echo '</div>';
    }

public function bg_color_field_callback() {
    $value = get_option( 'wctym_bg_color', '' ); // no default value
    echo '<input type="text" name="wctym_bg_color" value="' . esc_attr( $value ) . '" class="wctym-color-field" />';
    echo '<p class="description">Leave empty to use default background style from plugin.</p>';
}


public function text_color_field_callback() {
    $value = get_option( 'wctym_text_color', '' ); // no default value
    echo '<input type="text" name="wctym_text_color" value="' . esc_attr( $value ) . '" class="wctym-color-field" />';
    echo '<p class="description">Leave empty to use default text color from plugin CSS.</p>';
}


public function border_radius_field_callback() {
    $value = get_option( 'wctym_border_radius', '' );
    echo '<input type="number" name="wctym_border_radius" value="' . esc_attr( $value ) . '" placeholder="5" />';
    echo '<p class="description">Optional border radius in pixels. Leave blank for default.</p>';
}

}

new WCTYM_Settings_Page();
