<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 ADMIN SETTINGS REGISTRATION
  Description: Registers all configurable plugin options 
  Group: igny8_settings_group
==================================================*/

/**
 * Register all plugin settings on admin_init hook
 * This function handles the registration of all WordPress options
 * that the plugin uses for configuration
 */
add_action('admin_init', function () {

    // == Core OpenAI settings
    register_setting('igny8_settings_group', 'igny8_api_key');
    register_setting('igny8_settings_group', 'igny8_model');
    register_setting('igny8_settings_group', 'igny8_use_moderation');

    // == Input scope settings
    register_setting('igny8_settings_group', 'igny8_input_scope');

    // == Detection prompt customization
    register_setting('igny8_settings_group', 'igny8_detection_prompt', [
        'sanitize_callback' => 'wp_kses_post',
    ]);

    // == Rewrite settings
    register_setting('igny8_settings_group', 'igny8_content_length');
    register_setting('igny8_settings_group', 'igny8_rewrite_prompt', [
        'sanitize_callback' => 'wp_kses_post',
    ]);

    // == UI: Teaser text above button
    register_setting('igny8_settings_group', 'igny8_teaser_text', [
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    // == UI: Custom CSS injection
    register_setting('igny8_settings_group', 'igny8_custom_css', [
        'sanitize_callback' => 'wp_kses_post',
    ]);

    // == UI: Button and content background colors
    register_setting('igny8_settings_group', 'igny8_button_color', [
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_bg', [
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    // == Context injection options
    register_setting('igny8_settings_group', 'igny8_context_source', [
        'sanitize_callback' => 'wp_kses_post',
    ]);
    register_setting('igny8_settings_group', 'igny8_include_post_content', [
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    register_setting('igny8_settings_group', 'igny8_include_page_context', [
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    // == Field mode and fixed field config
    register_setting('igny8_settings_group', 'igny8_field_mode');
    register_setting('igny8_settings_group', 'igny8_fixed_fields_config');
});


/*==================================================
  ## IGNY8 SETTINGS MENU
  Description: Adds a submenu item under WordPress "Settings"
==================================================*/

/**
 * Add Igny8 settings page to WordPress admin menu
 * Creates a submenu item under Settings for plugin configuration
 */
add_action('admin_menu', function () {
    add_options_page(
        'Igny8 Settings',   // Page title
        'Igny8',            // Menu title
        'manage_options',   // Capability required
        'igny8',            // Menu slug
        'igny8_settings_page' // Callback function to render the page
    );
}); 