<?php
/*
Plugin Name: Igny8
Description: Inject AI-powered, persona-adaptive blog content via OpenAI with moderation and model selection.
Version: 2.2
Author: Igny8 Team
Text Domain: igny8
*/

defined('ABSPATH') || exit;

// Load all core modules
require_once plugin_dir_path(__FILE__) . 'install.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-ui.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-enqueue.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax.php';
require_once plugin_dir_path(__FILE__) . 'includes/openai.php';
require_once plugin_dir_path(__FILE__) . 'includes/db.php';
require_once plugin_dir_path(__FILE__) . 'includes/utils.php';

// Load frontend styling and dynamic color logic
if (!is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'includes/frontend-css.php';
}

// Register activation hook
register_activation_hook(__FILE__, 'igny8_install');
