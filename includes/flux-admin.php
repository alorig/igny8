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
                <li><a href="#flux-display">Display Settings</a></li>
                <li><a href="#flux-context">Context & Field Settings</a></li>
                <li><a href="#flux-generation">Content Generation Settings</a></li>
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
                        
                        <div class="igny8-tab-section">
                            <h3>Post Type Personalization Control</h3>
                            <p>Select which post types should have automatic personalization injection enabled.</p>
                            
                            <table class="form-table">
                                <?php
                                // Get all public post types, excluding attachments
                                $public_post_types = get_post_types(['public' => true], 'objects');
                                
                                foreach ($public_post_types as $post_type) {
                                    $post_type_name = $post_type->name;
                                    $post_type_label = $post_type->label;
                                    
                                    // Skip attachments
                                    if ($post_type_name === 'attachment') {
                                        continue;
                                    }
                                    
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
                        
                        <div class="igny8-placeholder">
                            <h4>Usage Snapshot</h4>
                            <p>This section will display usage statistics and performance metrics for the FLUX personalization engine.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Display Settings Tab -->
                <div id="flux-display" class="igny8-tab-content">
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
                                <th>Teaser Text:</th>
                                <td>
                                    <input type="text" name="igny8_flux_teaser_text" value="<?php echo esc_attr(get_option('igny8_flux_teaser_text', 'Want to read this as if it was written exclusively about you?')); ?>" size="80">
                                    <p class="description">Text displayed above the personalization button.</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Button Background Color:</th>
                                <td>
                                    <input type="text" name="igny8_flux_button_color" value="<?php echo esc_attr(get_option('igny8_flux_button_color', '#0073aa')); ?>" size="10">
                                    <p class="description">Background color for the personalization button.</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Content Background Color:</th>
                                <td>
                                    <input type="text" name="igny8_flux_content_bg" value="<?php echo esc_attr(get_option('igny8_flux_content_bg', '#f9f9f9')); ?>" size="10">
                                    <p class="description">Background color for personalized content.</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Custom CSS:</th>
                                <td>
                                    <textarea name="igny8_flux_custom_css" rows="5" cols="80"><?php echo esc_textarea(get_option('igny8_flux_custom_css', '')); ?></textarea>
                                    <p class="description">Custom CSS for FLUX personalization styling.</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Context & Field Settings Tab -->
                <div id="flux-context" class="igny8-tab-content">
                    <div class="igny8-tab-section">
                        <h3>Field Configuration</h3>
                        <table class="form-table">
                            <tr>
                                <th>Field Mode:</th>
                                <td>
                                    <select name="igny8_flux_field_mode">
                                        <option value="dynamic" <?php selected('dynamic', get_option('igny8_flux_field_mode', 'dynamic')); ?>>Auto Detect (GPT)</option>
                                        <option value="fixed" <?php selected('fixed', get_option('igny8_flux_field_mode')); ?>>Fixed Fields (No Detection)</option>
                                    </select>
                                    <p class="description">Choose how personalization fields should be generated.</p>
                                </td>
                            </tr>
                            <tr id="igny8-flux-fixed-fields-row">
                                <th>Fixed Fields:</th>
                                <td>
                                    <table id="igny8-flux-fixed-fields-table" class="widefat">
                                        <thead>
                                            <tr>
                                                <th>Label</th>
                                                <th>Type</th>
                                                <th>Options (comma-separated)</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $fields = get_option('igny8_flux_fixed_fields_config', []);
                                            if (empty($fields)) {
                                                $fields = [['label' => '', 'type' => 'text', 'options' => '']];
                                            }

                                            foreach ($fields as $i => $field) {
                                                echo '<tr>';
                                                echo '<td><input type="text" name="igny8_flux_fixed_fields_config[' . $i . '][label]" value="' . esc_attr($field['label']) . '"></td>';
                                                echo '<td>
                                                        <select name="igny8_flux_fixed_fields_config[' . $i . '][type]">
                                                          <option value="text" ' . selected($field['type'], 'text', false) . '>Text</option>
                                                          <option value="select" ' . selected($field['type'], 'select', false) . '>Select</option>
                                                          <option value="radio" ' . selected($field['type'], 'radio', false) . '>Radio</option>
                                                        </select>
                                                      </td>';
                                                echo '<td><input type="text" name="igny8_flux_fixed_fields_config[' . $i . '][options]" value="' . esc_attr($field['options']) . '"></td>';
                                                echo '<td><button type="button" class="button igny8-flux-remove-row">Remove</button></td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                    <button type="button" id="igny8-flux-add-row" class="button">Add Field</button>
                                    <p class="description">You can add up to 6 fields. Options apply only to select/radio.</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="igny8-tab-section">
                        <h3>Context Injection Settings</h3>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Include Page Content in Prompt</th>
                                <td>
                                    <input type="checkbox" name="igny8_flux_include_post_content" value="1" <?php checked(get_option('igny8_flux_include_post_content'), '1'); ?> />
                                    <label for="igny8_flux_include_post_content">Enable to include current page's content in the GPT prompt</label>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Include Page Context in Prompt</th>
                                <td>
                                    <input type="checkbox" name="igny8_flux_include_page_context" value="1" <?php checked(get_option('igny8_flux_include_page_context'), '1'); ?> />
                                    <label for="igny8_flux_include_page_context">Enable to include output from a context shortcode (e.g. [page_context])</label>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Context Shortcode or Manual Text</th>
                                <td>
                                    <textarea name="igny8_flux_context_source" rows="4" cols="60"><?php echo esc_textarea(get_option('igny8_flux_context_source')); ?></textarea>
                                    <p class="description">Enter a shortcode like <code>[page_context]</code> or any manual text you want injected when 'Include Page Context' is enabled.</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Input Scope for Field Detection:</th>
                                <td>
                                    <select name="igny8_flux_input_scope">
                                        <?php
                                        $scope = get_option('igny8_flux_input_scope', '300');
                                        $scopes = [
                                            'title' => 'Title Only',
                                            '300'   => 'First 300 Words',
                                            'full'  => 'Full Content',
                                        ];
                                        foreach ($scopes as $val => $label) {
                                            echo "<option value='$val'" . selected($scope, $val, false) . ">$label</option>";
                                        }
                                        ?>
                                    </select>
                                    <p class="description">Choose how much content to analyze for field detection.</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Detection Prompt:</th>
                                <td>
                                    <textarea name="igny8_flux_detection_prompt" rows="4" cols="80"><?php echo esc_textarea(get_option('igny8_flux_detection_prompt')); ?></textarea>
                                    <p class="description">Custom prompt for detecting personalization fields from content.</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Content Generation Settings Tab -->
                <div id="flux-generation" class="igny8-tab-content">
                    <div class="igny8-tab-section">
                        <h3>Content Generation Configuration</h3>
                        <table class="form-table">
                            <tr>
                                <th>Rewrite Content Length:</th>
                                <td>
                                    <select name="igny8_flux_content_length">
                                        <?php
                                        $length = get_option('igny8_flux_content_length', '300');
                                        $lengths = [
                                            '300' => '300 Words',
                                            '600' => '600 Words',
                                            '900' => '900 Words',
                                            'full' => 'Full Content',
                                        ];
                                        foreach ($lengths as $val => $label) {
                                            echo "<option value='$val'" . selected($length, $val, false) . ">$label</option>";
                                        }
                                        ?>
                                    </select>
                                    <p class="description">Choose the length of personalized content to generate.</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Rewrite Prompt:</th>
                                <td>
                                    <textarea name="igny8_flux_rewrite_prompt" rows="4" cols="80"><?php echo esc_textarea(get_option('igny8_flux_rewrite_prompt')); ?></textarea>
                                    <p class="description">Custom prompt for generating personalized content.</p>
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
    
    // Save display settings
    if (isset($_POST['igny8_flux_insertion_position'])) {
        $position = sanitize_text_field($_POST['igny8_flux_insertion_position']);
        update_option('igny8_flux_insertion_position', $position);
    }
    
    if (isset($_POST['igny8_flux_display_mode'])) {
        $mode = sanitize_text_field($_POST['igny8_flux_display_mode']);
        update_option('igny8_flux_display_mode', $mode);
    }
    
    if (isset($_POST['igny8_flux_teaser_text'])) {
        $teaser = sanitize_text_field($_POST['igny8_flux_teaser_text']);
        update_option('igny8_flux_teaser_text', $teaser);
    }
    
    if (isset($_POST['igny8_flux_button_color'])) {
        $button_color = sanitize_hex_color($_POST['igny8_flux_button_color']);
        update_option('igny8_flux_button_color', $button_color);
    }
    
    if (isset($_POST['igny8_flux_content_bg'])) {
        $content_bg = sanitize_hex_color($_POST['igny8_flux_content_bg']);
        update_option('igny8_flux_content_bg', $content_bg);
    }
    
    if (isset($_POST['igny8_flux_custom_css'])) {
        $custom_css = wp_kses_post($_POST['igny8_flux_custom_css']);
        update_option('igny8_flux_custom_css', $custom_css);
    }
    
    // Save field configuration
    if (isset($_POST['igny8_flux_field_mode'])) {
        $field_mode = sanitize_text_field($_POST['igny8_flux_field_mode']);
        update_option('igny8_flux_field_mode', $field_mode);
    }
    
    if (isset($_POST['igny8_flux_fixed_fields_config']) && is_array($_POST['igny8_flux_fixed_fields_config'])) {
        $fields = [];
        foreach ($_POST['igny8_flux_fixed_fields_config'] as $field) {
            if (!empty($field['label'])) {
                $fields[] = [
                    'label' => sanitize_text_field($field['label']),
                    'type' => sanitize_text_field($field['type']),
                    'options' => sanitize_text_field($field['options'])
                ];
            }
        }
        update_option('igny8_flux_fixed_fields_config', $fields);
    }
    
    // Save context settings
    $include_content = isset($_POST['igny8_flux_include_post_content']) ? '1' : '0';
    update_option('igny8_flux_include_post_content', $include_content);
    
    $include_context = isset($_POST['igny8_flux_include_page_context']) ? '1' : '0';
    update_option('igny8_flux_include_page_context', $include_context);
    
    if (isset($_POST['igny8_flux_context_source'])) {
        $context_source = wp_kses_post($_POST['igny8_flux_context_source']);
        update_option('igny8_flux_context_source', $context_source);
    }
    
    if (isset($_POST['igny8_flux_input_scope'])) {
        $input_scope = sanitize_text_field($_POST['igny8_flux_input_scope']);
        update_option('igny8_flux_input_scope', $input_scope);
    }
    
    if (isset($_POST['igny8_flux_detection_prompt'])) {
        $detection_prompt = wp_kses_post($_POST['igny8_flux_detection_prompt']);
        update_option('igny8_flux_detection_prompt', $detection_prompt);
    }
    
    // Save content generation settings
    if (isset($_POST['igny8_flux_content_length'])) {
        $content_length = sanitize_text_field($_POST['igny8_flux_content_length']);
        update_option('igny8_flux_content_length', $content_length);
    }
    
    if (isset($_POST['igny8_flux_rewrite_prompt'])) {
        $rewrite_prompt = wp_kses_post($_POST['igny8_flux_rewrite_prompt']);
        update_option('igny8_flux_rewrite_prompt', $rewrite_prompt);
    }
    
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
    // Global settings
    register_setting('igny8_flux_settings_group', 'igny8_flux_global_status');
    register_setting('igny8_flux_settings_group', 'igny8_flux_enabled_post_types');
    
    // Display settings
    register_setting('igny8_flux_settings_group', 'igny8_flux_insertion_position');
    register_setting('igny8_flux_settings_group', 'igny8_flux_display_mode');
    register_setting('igny8_flux_settings_group', 'igny8_flux_teaser_text');
    register_setting('igny8_flux_settings_group', 'igny8_flux_button_color');
    register_setting('igny8_flux_settings_group', 'igny8_flux_content_bg');
    register_setting('igny8_flux_settings_group', 'igny8_flux_custom_css');
    
    // Field configuration
    register_setting('igny8_flux_settings_group', 'igny8_flux_field_mode');
    register_setting('igny8_flux_settings_group', 'igny8_flux_fixed_fields_config');
    
    // Context settings
    register_setting('igny8_flux_settings_group', 'igny8_flux_include_post_content');
    register_setting('igny8_flux_settings_group', 'igny8_flux_include_page_context');
    register_setting('igny8_flux_settings_group', 'igny8_flux_context_source');
    register_setting('igny8_flux_settings_group', 'igny8_flux_input_scope');
    register_setting('igny8_flux_settings_group', 'igny8_flux_detection_prompt');
    
    // Content generation settings
    register_setting('igny8_flux_settings_group', 'igny8_flux_content_length');
    register_setting('igny8_flux_settings_group', 'igny8_flux_rewrite_prompt');
}); 