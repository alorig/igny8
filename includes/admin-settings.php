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
  ## IGNY8 TOP-LEVEL ADMIN MENU
  Description: Creates top-level menu with submenus for all Igny8 modules
==================================================*/

/**
 * Add Igny8 top-level menu and submenus to WordPress admin
 * Creates a complete admin menu structure for all Igny8 modules
 */
add_action('admin_menu', function () {
    // Add top-level menu
    add_menu_page(
        'Igny8 Dashboard',  // Page title
        'Igny8',            // Menu title
        'manage_options',   // Capability required
        'igny8',            // Menu slug
        'igny8_admin_page_loader', // Callback function to render the page
        'dashicons-admin-generic', // Icon
        30                  // Position
    );

    // Add submenus
    add_submenu_page(
        'igny8',            // Parent slug
        'Dashboard',        // Page title
        'Dashboard',        // Menu title
        'manage_options',   // Capability required
        'igny8',            // Menu slug (same as parent for first submenu)
        'igny8_admin_page_loader' // Callback function
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'FLUX',             // Page title
        'FLUX',             // Menu title
        'manage_options',   // Capability required
        'igny8-flux',       // Menu slug
        'igny8_admin_page_loader' // Callback function
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'INTELLI',          // Page title
        'INTELLI',          // Menu title
        'manage_options',   // Capability required
        'igny8-intelli',    // Menu slug
        'igny8_admin_page_loader' // Callback function
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'LOOPS',            // Page title
        'LOOPS',            // Menu title
        'manage_options',   // Capability required
        'igny8-loops',      // Menu slug
        'igny8_admin_page_loader' // Callback function
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'HIVE',             // Page title
        'HIVE',             // Menu title
        'manage_options',   // Capability required
        'igny8-hive',       // Menu slug
        'igny8_admin_page_loader' // Callback function
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'SKIN',             // Page title
        'SKIN',             // Menu title
        'manage_options',   // Capability required
        'igny8-skin',       // Menu slug
        'igny8_admin_page_loader' // Callback function
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'Settings',         // Page title
        'Settings',         // Menu title
        'manage_options',   // Capability required
        'igny8-settings',   // Menu slug
        'igny8_admin_page_loader' // Callback function
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'Reports',          // Page title
        'Reports',          // Menu title
        'manage_options',   // Capability required
        'igny8-reports',    // Menu slug
        'igny8_admin_page_loader' // Callback function
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'Help',             // Page title
        'Help',             // Menu title
        'manage_options',   // Capability required
        'igny8-help',       // Menu slug
        'igny8_admin_page_loader' // Callback function
    );
}); 