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
    $form_submitted = false;
    $nonce_valid = false;
    
    if (!empty($_POST)) {
        // Check for various submit indicators
        if (isset($_POST['submit']) || isset($_POST['save-flux-settings']) || isset($_POST['igny8_flux_nonce'])) {
            $form_submitted = true;
            
            // Verify nonce
            if (isset($_POST['igny8_flux_nonce']) && wp_verify_nonce($_POST['igny8_flux_nonce'], 'igny8_flux_settings')) {
                $nonce_valid = true;
                igny8_flux_save_settings();
            } else {
                // Show nonce error
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error is-dismissible"><p>Security check failed. Please try again.</p></div>';
                });
            }
        }
    }
    
    // Check if settings were just saved and show success message
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
        echo '<div class="notice notice-success is-dismissible"><p>FLUX settings saved successfully.</p></div>';
    }
    
    // Get current settings
    $enabled_post_types = get_option('igny8_flux_enabled_post_types', []);
    $flux_status = get_option('igny8_flux_global_status', 'enabled');
    
    // Debug information for administrators
    if (current_user_can('manage_options') && isset($_GET['debug'])) {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>Debug Information:</strong></p>';
        echo '<p>Saved enabled post types: ' . implode(', ', $enabled_post_types) . '</p>';
        echo '<p>Global FLUX status: ' . esc_html($flux_status) . '</p>';
        echo '<p>Total saved post types count: ' . count($enabled_post_types) . '</p>';
        
        // Show POST data if available
        if (!empty($_POST)) {
            echo '<h4>POST Data Received:</h4>';
            echo '<table class="widefat" style="margin-top: 10px;">';
            echo '<thead><tr><th>Field</th><th>Value</th></tr></thead><tbody>';
            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                echo '<tr><td><strong>' . esc_html($key) . '</strong></td><td>' . esc_html($value) . '</td></tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p><strong>No POST data received</strong></p>';
        }
        
        // Show form submission status
        echo '<h4>Form Submission Status:</h4>';
        echo '<p>POST data present: ' . (!empty($_POST) ? 'YES' : 'NO') . '</p>';
        echo '<p>Submit button pressed: ' . (isset($_POST['submit']) ? 'YES' : 'NO') . '</p>';
        echo '<p>Save FLUX settings pressed: ' . (isset($_POST['save-flux-settings']) ? 'YES' : 'NO') . '</p>';
        echo '<p>Nonce present: ' . (isset($_POST['igny8_flux_nonce']) ? 'YES' : 'NO') . '</p>';
        if (isset($_POST['igny8_flux_nonce'])) {
            echo '<p>Nonce valid: ' . (wp_verify_nonce($_POST['igny8_flux_nonce'], 'igny8_flux_settings') ? 'YES' : 'NO') . '</p>';
        }
        
        // Show all FLUX field values
        $flux_fields = [
            'igny8_flux_global_status' => 'Global Status',
            'igny8_flux_insertion_position' => 'Insertion Position',
            'igny8_flux_display_mode' => 'Display Mode',
            'igny8_flux_teaser_text' => 'Teaser Text',
            'igny8_flux_button_color' => 'Button Color',
            'igny8_flux_content_bg' => 'Content Background',
            'igny8_flux_custom_css' => 'Custom CSS',
            'igny8_flux_field_mode' => 'Field Mode',
            'igny8_flux_include_post_content' => 'Include Post Content',
            'igny8_flux_include_page_context' => 'Include Page Context',
            'igny8_flux_context_source' => 'Context Source',
            'igny8_flux_input_scope' => 'Input Scope',
            'igny8_flux_detection_prompt' => 'Detection Prompt',
            'igny8_flux_content_length' => 'Content Length',
            'igny8_flux_rewrite_prompt' => 'Rewrite Prompt'
        ];
        
        echo '<h4>All FLUX Field Values:</h4>';
        echo '<table class="widefat" style="margin-top: 10px;">';
        echo '<thead><tr><th>Field</th><th>Value</th></tr></thead><tbody>';
        
        foreach ($flux_fields as $option_name => $label) {
            $value = get_option($option_name, 'Not Set');
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            if (empty($value)) {
                $value = 'Empty';
            }
            echo '<tr><td><strong>' . esc_html($label) . '</strong></td><td>' . esc_html($value) . '</td></tr>';
        }
        
        // Show fixed fields config
        $fixed_fields = get_option('igny8_flux_fixed_fields_config', []);
        echo '<tr><td><strong>Fixed Fields Config</strong></td><td>' . esc_html(json_encode($fixed_fields)) . '</td></tr>';
        
        echo '</tbody></table>';
        echo '</div>';
    }
    
    // Test save function
    if (current_user_can('manage_options') && isset($_GET['test_save'])) {
        $test_result = igny8_flux_test_saving();
        if ($test_result['success']) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>Test Result: PASSED ✓</strong></p>';
            echo '<p>All FLUX settings and field saving are working correctly.</p>';
            echo '<p>Detailed results:</p>';
            foreach ($test_result['results'] as $option => $status) {
                echo '<p>' . esc_html($option) . ': ' . esc_html($status) . '</p>';
            }
            echo '</div>';
        } else {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p><strong>Test Result: FAILED ✗</strong></p>';
            echo '<p>Some FLUX settings or field saving are not working as expected.</p>';
            echo '<p>Detailed results:</p>';
            foreach ($test_result['results'] as $option => $status) {
                echo '<p>' . esc_html($option) . ': ' . esc_html($status) . '</p>';
            }
            echo '</div>';
        }
    }
    
    ?>
    <div class="wrap">
        <h2>FLUX Module</h2>
        
        <?php if (current_user_can('manage_options')): ?>
        <p>
            <a href="?page=igny8-flux&debug=1" class="button">Show Debug Info</a>
            <a href="?page=igny8-flux" class="button">Hide Debug Info</a>
            <a href="?page=igny8-flux&test_save=1" class="button">Test Save Function</a>
        </p>
        <?php endif; ?>
        
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
                <input type="hidden" name="save-flux-settings" value="1">
                
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
                                    $checked_attr = $is_enabled ? 'checked' : '';
                                    $status_class = $is_enabled ? 'enabled' : 'disabled';
                                    $status_text = $is_enabled ? '✓ Enabled' : '✗ Disabled';
                                    ?>
                                    <tr class="post-type-row <?php echo $status_class; ?>">
                                        <th><?php echo esc_html($post_type_label); ?>:</th>
                                        <td>
                                            <label>
                                                <input type="checkbox" 
                                                       name="igny8_flux_enabled_post_types[]" 
                                                       value="<?php echo esc_attr($post_type_name); ?>"
                                                       <?php echo $checked_attr; ?>>
                                                Enable personalization for <?php echo esc_html(strtolower($post_type_label)); ?>
                                            </label>
                                            <p class="description">
                                                When enabled, the [igny8] shortcode will be automatically injected before <?php echo esc_html(strtolower($post_type_label)); ?> content.
                                                <br><strong>Current status: <?php echo $status_text; ?></strong>
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
    
    // TEMPORARY DEBUG LOGGING - Remove after diagnosis
    error_log('=== FLUX SAVE DEBUG START ===');
    error_log('POST data received: ' . print_r($_POST, true));
    error_log('Nonce verification: ' . (isset($_POST['igny8_flux_nonce']) && wp_verify_nonce($_POST['igny8_flux_nonce'], 'igny8_flux_settings') ? 'PASS' : 'FAIL'));
    
    // Log current saved values before saving
    $flux_fields = [
        'igny8_flux_global_status',
        'igny8_flux_enabled_post_types',
        'igny8_flux_insertion_position',
        'igny8_flux_display_mode',
        'igny8_flux_teaser_text',
        'igny8_flux_button_color',
        'igny8_flux_content_bg',
        'igny8_flux_custom_css',
        'igny8_flux_field_mode',
        'igny8_flux_include_post_content',
        'igny8_flux_include_page_context',
        'igny8_flux_context_source',
        'igny8_flux_input_scope',
        'igny8_flux_detection_prompt',
        'igny8_flux_content_length',
        'igny8_flux_rewrite_prompt'
    ];
    
    error_log('Current saved values before saving:');
    foreach ($flux_fields as $field) {
        $value = get_option($field, 'NOT_SET');
        error_log("$field: " . (is_array($value) ? json_encode($value) : $value));
    }
    
    // Define field mappings for clean processing
    $text_fields = [
        'igny8_flux_global_status' => 'enabled',
        'igny8_flux_insertion_position' => 'before',
        'igny8_flux_display_mode' => 'button',
        'igny8_flux_teaser_text' => 'Want to read this as if it was written exclusively about you?',
        'igny8_flux_button_color' => '#0073aa',
        'igny8_flux_content_bg' => '#f9f9f9',
        'igny8_flux_field_mode' => 'dynamic',
        'igny8_flux_input_scope' => '300',
        'igny8_flux_content_length' => '300'
    ];
    
    $textarea_fields = [
        'igny8_flux_custom_css' => '',
        'igny8_flux_detection_prompt' => '',
        'igny8_flux_rewrite_prompt' => '',
        'igny8_flux_context_source' => ''
    ];
    
    $checkbox_fields = [
        'igny8_flux_include_post_content' => '0',
        'igny8_flux_include_page_context' => '0'
    ];
    
    error_log('Processing text fields:');
    // Process text fields
    foreach ($text_fields as $field_name => $default_value) {
        if (isset($_POST[$field_name])) {
            $value = sanitize_text_field($_POST[$field_name]);
            update_option($field_name, $value);
            error_log("Saved $field_name: $value");
        } else {
            update_option($field_name, $default_value);
            error_log("Set default for $field_name: $default_value (not in POST)");
        }
    }
    
    error_log('Processing textarea fields:');
    // Process textarea fields
    foreach ($textarea_fields as $field_name => $default_value) {
        if (isset($_POST[$field_name])) {
            $value = wp_kses_post($_POST[$field_name]);
            update_option($field_name, $value);
            error_log("Saved $field_name: " . substr($value, 0, 50) . "...");
        } else {
            update_option($field_name, $default_value);
            error_log("Set default for $field_name: $default_value (not in POST)");
        }
    }
    
    error_log('Processing checkbox fields:');
    // Process checkbox fields
    foreach ($checkbox_fields as $field_name => $default_value) {
        $value = isset($_POST[$field_name]) ? '1' : '0';
        update_option($field_name, $value);
        error_log("Saved $field_name: $value (checkbox)");
    }
    
    // Handle color fields with hex validation
    if (isset($_POST['igny8_flux_button_color'])) {
        $button_color = sanitize_hex_color($_POST['igny8_flux_button_color']);
        if ($button_color) {
            update_option('igny8_flux_button_color', $button_color);
            error_log("Saved button color: $button_color");
        }
    }
    
    if (isset($_POST['igny8_flux_content_bg'])) {
        $content_bg = sanitize_hex_color($_POST['igny8_flux_content_bg']);
        if ($content_bg) {
            update_option('igny8_flux_content_bg', $content_bg);
            error_log("Saved content bg: $content_bg");
        }
    }
    
    // Save enabled post types with comprehensive validation
    $enabled_post_types = [];
    
    if (isset($_POST['igny8_flux_enabled_post_types']) && is_array($_POST['igny8_flux_enabled_post_types'])) {
        // Get all valid public post types for validation
        $valid_post_types = array_keys(get_post_types(['public' => true], 'names'));
        
        // Sanitize and validate each post type
        foreach ($_POST['igny8_flux_enabled_post_types'] as $post_type) {
            $sanitized_type = sanitize_text_field($post_type);
            
            // Only add if it's not empty and is a valid post type (excluding attachments)
            if (!empty($sanitized_type) && in_array($sanitized_type, $valid_post_types) && $sanitized_type !== 'attachment') {
                $enabled_post_types[] = $sanitized_type;
            }
        }
        error_log("Saved enabled post types: " . implode(', ', $enabled_post_types));
    } else {
        error_log("No post types selected - saving empty array");
    }
    
    // Save the validated post types
    update_option('igny8_flux_enabled_post_types', $enabled_post_types);
    
    // Save fixed fields configuration
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
        error_log("Saved fixed fields config: " . json_encode($fields));
    } else {
        error_log("No fixed fields config in POST");
    }
    
    // Log final saved values
    error_log('Final saved values after saving:');
    foreach ($flux_fields as $field) {
        $value = get_option($field, 'NOT_SET');
        error_log("$field: " . (is_array($value) ? json_encode($value) : $value));
    }
    
    error_log('=== FLUX SAVE DEBUG END ===');
    
    // Show success message with detailed information
    add_action('admin_notices', function() use ($enabled_post_types) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>FLUX settings saved successfully!</strong></p>';
        echo '<p>Enabled post types: ' . (empty($enabled_post_types) ? 'None' : implode(', ', $enabled_post_types)) . '</p>';
        echo '<p>Total enabled: ' . count($enabled_post_types) . '</p>';
        echo '</div>';
    });
}

/**
 * Test function to verify FLUX field saving works correctly
 * This function can be called manually for testing purposes
 */
function igny8_flux_test_saving() {
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    // Test data for all FLUX fields
    $test_data = [
        'igny8_flux_global_status' => 'enabled',
        'igny8_flux_enabled_post_types' => ['post', 'page'],
        'igny8_flux_insertion_position' => 'before',
        'igny8_flux_display_mode' => 'button',
        'igny8_flux_teaser_text' => 'Test teaser text',
        'igny8_flux_button_color' => '#ff0000',
        'igny8_flux_content_bg' => '#f0f0f0',
        'igny8_flux_custom_css' => '/* Test CSS */',
        'igny8_flux_field_mode' => 'dynamic',
        'igny8_flux_fixed_fields_config' => [
            ['label' => 'Test Field', 'type' => 'text', 'options' => '']
        ],
        'igny8_flux_include_post_content' => '1',
        'igny8_flux_include_page_context' => '0',
        'igny8_flux_context_source' => '[test_context]',
        'igny8_flux_input_scope' => '300',
        'igny8_flux_detection_prompt' => 'Test detection prompt',
        'igny8_flux_content_length' => '600',
        'igny8_flux_rewrite_prompt' => 'Test rewrite prompt'
    ];
    
    // Save test data
    foreach ($test_data as $option_name => $value) {
        update_option($option_name, $value);
    }
    
    // Retrieve and verify each field
    $all_passed = true;
    $results = [];
    
    foreach ($test_data as $option_name => $expected_value) {
        $saved_value = get_option($option_name);
        
        if ($saved_value === $expected_value) {
            $results[$option_name] = 'PASS';
        } else {
            $results[$option_name] = 'FAIL';
            $all_passed = false;
        }
    }
    
    // Return results for display
    return [
        'success' => $all_passed,
        'results' => $results,
        'test_data' => $test_data
    ];
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