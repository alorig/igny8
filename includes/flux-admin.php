<?php
defined('ABSPATH') || exit;

/*==================================================
  ## FLUX ADMIN MODULE
  Description: FLUX personalization engine admin interface
==================================================*/

/**
 * FLUX admin page renderer
 * Handles all FLUX module admin interface and settings
 */
function igny8_flux_admin_page() {
    // Handle form submission
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['igny8_flux_nonce'], 'igny8_flux_settings')) {
        igny8_flux_save_settings();
    }
    
    // Get current settings
    $enabled_post_types = get_option('igny8_flux_enabled_post_types', []);
    $flux_status = get_option('igny8_flux_global_status', 'enabled');
    
    ?>
    <div class="wrap">
        <h2>FLUX Module</h2>
        
        <div class="igny8-tabs">
            <ul class="igny8-tab-nav">
                <li><a href="#flux-overview">Overview</a></li>
                <li><a href="#flux-post-types">Enable Per Post Type</a></li>
                <li><a href="#flux-behavior">Personalization Behavior</a></li>
                <li><a href="#flux-debug">Debug & Insights</a></li>
            </ul>
            
            <form method="post" action="" class="igny8-settings-form">
                <?php wp_nonce_field('igny8_flux_settings', 'igny8_flux_nonce'); ?>
                
                <!-- Overview Tab -->
                <div id="flux-overview" class="igny8-tab-content">
                    <div class="igny8-tab-section">
                        <h3>FLUX Module Overview</h3>
                        <div class="igny8-placeholder">
                            <h4>FLUX Personalization Engine</h4>
                            <p>This section provides an overview of the FLUX personalization engine, including current status, active rules, and performance metrics.</p>
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>Global FLUX Status:</th>
                                <td>
                                    <select name="igny8_flux_global_status">
                                        <option value="enabled" <?php selected($flux_status, 'enabled'); ?>>Enabled</option>
                                        <option value="disabled" <?php selected($flux_status, 'disabled'); ?>>Disabled</option>
                                    </select>
                                    <p class="description">Control whether FLUX personalization is active globally.</p>
                                </td>
                            </tr>
                        </table>
                        
                        <div class="igny8-placeholder">
                            <h4>Usage Snapshot</h4>
                            <p>This section will display usage statistics and performance metrics for the FLUX personalization engine.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Enable Per Post Type Tab -->
                <div id="flux-post-types" class="igny8-tab-content">
                    <div class="igny8-tab-section">
                        <h3>Post Type Personalization Control</h3>
                        <p>Select which post types should have automatic personalization injection enabled.</p>
                        
                        <table class="form-table">
                            <?php
                            $public_post_types = get_post_types(['public' => true], 'objects');
                            
                            foreach ($public_post_types as $post_type) {
                                $post_type_name = $post_type->name;
                                $post_type_label = $post_type->label;
                                $is_enabled = in_array($post_type_name, $enabled_post_types);
                                ?>
                                <tr>
                                    <th><?php echo esc_html($post_type_label); ?>:</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" 
                                                   name="igny8_flux_enabled_post_types[]" 
                                                   value="<?php echo esc_attr($post_type_name); ?>"
                                                   <?php checked($is_enabled); ?>>
                                            Enable personalization for <?php echo esc_html(strtolower($post_type_label)); ?>
                                        </label>
                                        <p class="description">
                                            When enabled, the [igny8] shortcode will be automatically injected before <?php echo esc_html(strtolower($post_type_label)); ?> content.
                                        </p>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
                
                <!-- Personalization Behavior Tab -->
                <div id="flux-behavior" class="igny8-tab-content">
                    <div class="igny8-tab-section">
                        <h3>Personalization Display Settings</h3>
                        <table class="form-table">
                            <tr>
                                <th>Insertion Position:</th>
                                <td>
                                    <select name="igny8_flux_insertion_position">
                                        <?php
                                        $position = get_option('igny8_flux_insertion_position', 'before');
                                        $positions = [
                                            'before' => 'Before content (prepend)',
                                            'after' => 'After content (append)',
                                            'replace' => 'Replace content (experimental)'
                                        ];
                                        foreach ($positions as $value => $label) {
                                            echo '<option value="' . esc_attr($value) . '" ' . selected($position, $value, false) . '>' . esc_html($label) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <p class="description">Choose where the personalization content should be inserted relative to the main content.</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Display Mode:</th>
                                <td>
                                    <select name="igny8_flux_display_mode">
                                        <?php
                                        $mode = get_option('igny8_flux_display_mode', 'button');
                                        $modes = [
                                            'button' => 'Show personalization button',
                                            'inline' => 'Show inline personalization form',
                                            'auto' => 'Auto-personalize content'
                                        ];
                                        foreach ($modes as $value => $label) {
                                            echo '<option value="' . esc_attr($value) . '" ' . selected($mode, $value, false) . '>' . esc_html($label) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <p class="description">Choose how personalization should be presented to users.</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Personalization Prompt:</th>
                                <td>
                                    <textarea name="igny8_flux_personalization_prompt" rows="4" cols="80"><?php echo esc_textarea(get_option('igny8_flux_personalization_prompt', 'Personalize this content for me')); ?></textarea>
                                    <p class="description">Custom prompt text for personalization requests.</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Debug & Insights Tab -->
                <div id="flux-debug" class="igny8-tab-content">
                    <div class="igny8-tab-section">
                        <h3>Debug & Analytics</h3>
                        <div class="igny8-placeholder">
                            <h4>Personalization Triggers</h4>
                            <p>This section will display current personalization triggers fired and basic usage counts for debugging.</p>
                        </div>
                        
                        <div class="igny8-placeholder">
                            <h4>Advanced Debug Logs</h4>
                            <p>This section will provide debugging tools and usage insights for the FLUX personalization engine.</p>
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>Debug Mode:</th>
                                <td>
                                    <label>
                                        <input type="checkbox" 
                                               name="igny8_flux_debug_mode" 
                                               value="1" 
                                               <?php checked(get_option('igny8_flux_debug_mode'), '1'); ?>>
                                        Enable debug logging
                                    </label>
                                    <p class="description">Enable detailed logging for troubleshooting personalization issues.</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Save Button -->
                <div class="igny8-save-container">
                    <?php submit_button('Save FLUX Settings'); ?>
                </div>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Save FLUX settings
 */
function igny8_flux_save_settings() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Save global status
    if (isset($_POST['igny8_flux_global_status'])) {
        $status = sanitize_text_field($_POST['igny8_flux_global_status']);
        update_option('igny8_flux_global_status', $status);
    }
    
    // Save enabled post types
    if (isset($_POST['igny8_flux_enabled_post_types']) && is_array($_POST['igny8_flux_enabled_post_types'])) {
        $enabled_types = array_map('sanitize_text_field', $_POST['igny8_flux_enabled_post_types']);
        update_option('igny8_flux_enabled_post_types', $enabled_types);
    } else {
        update_option('igny8_flux_enabled_post_types', []);
    }
    
    // Save insertion position
    if (isset($_POST['igny8_flux_insertion_position'])) {
        $position = sanitize_text_field($_POST['igny8_flux_insertion_position']);
        update_option('igny8_flux_insertion_position', $position);
    }
    
    // Save display mode
    if (isset($_POST['igny8_flux_display_mode'])) {
        $mode = sanitize_text_field($_POST['igny8_flux_display_mode']);
        update_option('igny8_flux_display_mode', $mode);
    }
    
    // Save personalization prompt
    if (isset($_POST['igny8_flux_personalization_prompt'])) {
        $prompt = sanitize_textarea_field($_POST['igny8_flux_personalization_prompt']);
        update_option('igny8_flux_personalization_prompt', $prompt);
    }
    
    // Save debug mode
    $debug_mode = isset($_POST['igny8_flux_debug_mode']) ? '1' : '0';
    update_option('igny8_flux_debug_mode', $debug_mode);
    
    // Show success message
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p>FLUX settings saved successfully.</p></div>';
    });
}

/**
 * Frontend content filter to inject FLUX personalization
 */
function igny8_flux_inject_shortcode($content) {
    // Only run on frontend single views
    if (!is_singular() || is_admin() || is_archive()) {
        return $content;
    }
    
    // Check if FLUX is globally enabled
    $flux_status = get_option('igny8_flux_global_status', 'enabled');
    if ($flux_status !== 'enabled') {
        return $content;
    }
    
    // Get current post type
    $post_type = get_post_type();
    if (!$post_type) {
        return $content;
    }
    
    // Check if this post type is enabled for FLUX
    $enabled_post_types = get_option('igny8_flux_enabled_post_types', []);
    if (!in_array($post_type, $enabled_post_types)) {
        return $content;
    }
    
    // Get insertion position
    $position = get_option('igny8_flux_insertion_position', 'before');
    
    // Generate the shortcode output
    $shortcode_output = do_shortcode('[igny8]');
    
    // Insert based on position
    switch ($position) {
        case 'before':
            return $shortcode_output . $content;
        case 'after':
            return $content . $shortcode_output;
        case 'replace':
            return $shortcode_output;
        default:
            return $content;
    }
}

// Hook the content filter
add_filter('the_content', 'igny8_flux_inject_shortcode');

/**
 * Register FLUX settings
 */
add_action('admin_init', function() {
    register_setting('igny8_flux_settings_group', 'igny8_flux_global_status');
    register_setting('igny8_flux_settings_group', 'igny8_flux_enabled_post_types');
    register_setting('igny8_flux_settings_group', 'igny8_flux_insertion_position');
    register_setting('igny8_flux_settings_group', 'igny8_flux_display_mode');
    register_setting('igny8_flux_settings_group', 'igny8_flux_personalization_prompt');
    register_setting('igny8_flux_settings_group', 'igny8_flux_debug_mode');
}); 