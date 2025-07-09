<?php
defined('ABSPATH') || exit;

add_action('wp_footer', 'igny8_output_custom_css');

function igny8_output_custom_css() {
    $custom_css = trim(get_option('igny8_custom_css', ''));
    $btn_color  = get_option('igny8_button_color', '#0073aa');
    $bg_color   = get_option('igny8_content_bg', '#f9f9f9');

    echo "<style id='igny8-custom-style'>
        .igny8-final-content {
            background-color: {$bg_color};
        }
        #igny8-form button.button {
            background-color: {$btn_color};
        }
        {$custom_css}
    </style>";
}
