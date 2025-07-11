<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 ADMIN PAGE UI
  Description: Admin pages for all Igny8 modules
==================================================*/

/**
 * Admin page loader function
 * Routes to appropriate page based on current submenu
 */
function igny8_admin_page_loader() {
    $current_page = $_GET['page'] ?? 'igny8';
    
    switch ($current_page) {
        case 'igny8':
            igny8_dashboard_page();
            break;
        case 'igny8-flux':
            igny8_flux_page();
            break;
        case 'igny8-intelli':
            igny8_intelli_page();
            break;
        case 'igny8-loops':
            igny8_loops_page();
            break;
        case 'igny8-hive':
            igny8_hive_page();
            break;
        case 'igny8-skin':
            igny8_skin_page();
            break;
        case 'igny8-settings':
            igny8_settings_page();
            break;
        case 'igny8-reports':
            igny8_reports_page();
            break;
        case 'igny8-help':
            igny8_help_page();
            break;
        default:
            igny8_dashboard_page();
            break;
    }
}

/**
 * Dashboard page
 */
function igny8_dashboard_page() {
    ?>
    <div class="wrap">
        <h2>Igny8 Dashboard</h2>
        <p><strong>Dashboard Loaded</strong></p>
        <p>Welcome to the Igny8 Dashboard. This is the main control center for all Igny8 modules.</p>
    </div>
    <?php
}

/**
 * FLUX module page
 */
function igny8_flux_page() {
    ?>
    <div class="wrap">
        <h2>FLUX Module</h2>
        <p><strong>FLUX Module Loaded</strong></p>
        <p>This is the FLUX module page for Igny8.</p>
    </div>
    <?php
}

/**
 * INTELLI module page
 */
function igny8_intelli_page() {
    ?>
    <div class="wrap">
        <h2>INTELLI Module</h2>
        <p><strong>INTELLI Module Loaded</strong></p>
        <p>This is the INTELLI module page for Igny8.</p>
    </div>
    <?php
}

/**
 * LOOPS module page
 */
function igny8_loops_page() {
    ?>
    <div class="wrap">
        <h2>LOOPS Module</h2>
        <p><strong>LOOPS Module Loaded</strong></p>
        <p>This is the LOOPS module page for Igny8.</p>
    </div>
    <?php
}

/**
 * HIVE module page
 */
function igny8_hive_page() {
    ?>
    <div class="wrap">
        <h2>HIVE Module</h2>
        <p><strong>HIVE Module Loaded</strong></p>
        <p>This is the HIVE module page for Igny8.</p>
    </div>
    <?php
}

/**
 * SKIN module page
 */
function igny8_skin_page() {
    ?>
    <div class="wrap">
        <h2>SKIN Module</h2>
        <p><strong>SKIN Module Loaded</strong></p>
        <p>This is the SKIN module page for Igny8.</p>
    </div>
    <?php
}

/**
 * Reports page
 */
function igny8_reports_page() {
    ?>
    <div class="wrap">
        <h2>Reports</h2>
        <p><strong>Reports Loaded</strong></p>
        <p>This is the Reports page for Igny8.</p>
    </div>
    <?php
}

/**
 * Help page
 */
function igny8_help_page() {
    ?>
    <div class="wrap">
        <h2>Help</h2>
        <p><strong>Help Loaded</strong></p>
        <p>This is the Help page for Igny8.</p>
    </div>
    <?php
}

/**
 * Render the main Igny8 settings page
 * This function outputs the complete HTML form for plugin configuration
 * including all form fields, validation, and user interface elements
 */
function igny8_settings_page() {
    ?>
    <div class="wrap">
        <h2>Igny8 Settings</h2>
        <form method="post" action="options.php">

           <?php
           // == Register settings group and sections ==
           settings_fields('igny8_settings_group');
           do_settings_sections('igny8_settings_group');
           ?>

            <table class="form-table">

                <!--== OpenAI API Key ==-->
                <tr>
                    <th>OpenAI API Key:</th>
                    <td><input type="text" name="igny8_api_key" value="<?php echo esc_attr(get_option('igny8_api_key')); ?>" size="50"></td>
                </tr>

                <!--== Model Selector ==-->
                <tr>
                    <th>Model:</th>
                    <td>
                        <select name="igny8_model">
                            <?php
                            $models = [
                                'gpt-4.1'         => 'gpt-4.1 ($2 in / $8 out)',
                                'gpt-4.1-mini'    => 'gpt-4.1-mini ($0.40 in / $1.60 out)',
                                'gpt-4.1-nano'    => 'gpt-4.1-nano ($0.10 in / $0.40 out)',
                                'gpt-4.5-preview' => 'gpt-4.5-preview ($75 in / $150 out)',
                                'gpt-3.5-turbo'   => 'gpt-3.5-turbo (Free for testing)',
                            ];
                            $selected = get_option('igny8_model', 'gpt-3.5-turbo');
                            foreach ($models as $val => $label) {
                                echo "<option value='$val'" . selected($selected, $val, false) . ">$label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <!--== API Test Button ==-->
                <tr>
                    <th>Check Connection:</th>
                    <td>
                        <button type="button" class="button" id="igny8-test-api">Check API Connection</button>
                        <p id="igny8-api-status" style="margin-top: 0.5em;"></p>
                    </td>
                </tr>

                <!--== Moderation Toggle ==-->
                <tr>
                    <th>Use Moderation:</th>
                    <td><input type="checkbox" name="igny8_use_moderation" value="1" <?php checked(1, get_option('igny8_use_moderation'), true); ?>></td>
                </tr>

                <!--== Input Scope ==-->
                <tr>
                    <th>Input Scope for Field Detection:</th>
                    <td>
                        <select name="igny8_input_scope">
                            <?php
                            $scopes = [
                                'title' => 'Title Only',
                                '300'   => 'First 300 Words',
                                'full'  => 'Full Content',
                            ];
                            $selected = get_option('igny8_input_scope', '300');
                            foreach ($scopes as $val => $label) {
                                echo "<option value='$val'" . selected($selected, $val, false) . ">$label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <!--== Detection Prompt ==-->
                <tr>
                    <th>Detection Prompt:</th>
                    <td><textarea name="igny8_detection_prompt" rows="4" cols="80"><?php echo esc_textarea(get_option('igny8_detection_prompt')); ?></textarea></td>
                </tr>

                <!--== Include Page Content ==-->
                <tr valign="top">
                    <th scope="row">Include Page Content in Prompt</th>
                    <td>
                        <input type="checkbox" name="igny8_include_post_content" value="1" <?php checked(get_option('igny8_include_post_content'), '1'); ?> />
                        <label for="igny8_include_post_content">Enable to include current page's content in the GPT prompt</label>
                    </td>
                </tr>

                <!--== Include Page Context ==-->
                <tr valign="top">
                    <th scope="row">Include Page Context in Prompt</th>
                    <td>
                        <input type="checkbox" name="igny8_include_page_context" value="1" <?php checked(get_option('igny8_include_page_context'), '1'); ?> />
                        <label for="igny8_include_page_context">Enable to include output from a context shortcode (e.g. [page_context])</label>
                    </td>
                </tr>

                <!--== Context Shortcode or Manual Text ==-->
                <tr valign="top">
                    <th scope="row">Context Shortcode or Manual Text</th>
                    <td>
                        <textarea name="igny8_context_source" rows="4" cols="60"><?php echo esc_textarea(get_option('igny8_context_source')); ?></textarea>
                        <p class="description">Enter a shortcode like <code>[page_context]</code> or any manual text you want injected when 'Include Page Context' is enabled.</p>
                    </td>
                </tr>

                <!--== Rewrite Content Length ==-->
                <tr>
                    <th>Rewrite Content Length:</th>
                    <td>
                        <select name="igny8_content_length">
                            <?php
                            $lengths = [
                                '300' => '300 Words',
                                '600' => '600 Words',
                                '900' => '900 Words',
                                'full' => 'Full Content',
                            ];
                            $selected = get_option('igny8_content_length', '300');
                            foreach ($lengths as $val => $label) {
                                echo "<option value='$val'" . selected($selected, $val, false) . ">$label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <!--== Rewrite Prompt ==-->
                <tr>
                    <th>Rewrite Prompt:</th>
                    <td><textarea name="igny8_rewrite_prompt" rows="4" cols="80"><?php echo esc_textarea(get_option('igny8_rewrite_prompt')); ?></textarea></td>
                </tr>

                <!--== Teaser Text ==-->
                <tr>
                    <th>Teaser Text:</th>
                    <td><input type="text" name="igny8_teaser_text" value="<?php echo esc_attr(get_option('igny8_teaser_text', 'Want to read this as if it was written exclusively about you?')); ?>" size="80"></td>
                </tr>

                <!--== Button Color ==-->
                <tr>
                    <th>Button Background Color:</th>
                    <td><input type="text" name="igny8_button_color" value="<?php echo esc_attr(get_option('igny8_button_color', '#0073aa')); ?>" size="10"></td>
                </tr>

                <!--== Content Background Color ==-->
                <tr>
                    <th>Content Background Color:</th>
                    <td><input type="text" name="igny8_content_bg" value="<?php echo esc_attr(get_option('igny8_content_bg', '#f9f9f9')); ?>" size="10"></td>
                </tr>

                <!--== Custom CSS ==-->
                <tr>
                    <th>Custom CSS:</th>
                    <td><textarea name="igny8_custom_css" rows="5" cols="80"><?php echo esc_textarea(get_option('igny8_custom_css', '')); ?></textarea></td>
                </tr>

                <!--== Field Mode Selection ==-->
                <tr>
                    <th>Field Mode:</th>
                    <td>
                        <select name="igny8_field_mode">
                            <option value="dynamic" <?php selected('dynamic', get_option('igny8_field_mode', 'dynamic')); ?>>Auto Detect (GPT)</option>
                            <option value="fixed" <?php selected('fixed', get_option('igny8_field_mode')); ?>>Fixed Fields (No Detection)</option>
                        </select>
                    </td>
                </tr>

                <!--== Fixed Fields Configuration Table ==-->
                <tr id="igny8-fixed-fields-row">
                    <th>Fixed Fields:</th>
                    <td>
                        <table id="igny8-fixed-fields-table" class="widefat">
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
                                $fields = get_option('igny8_fixed_fields_config', []);
                                if (empty($fields)) {
                                    $fields = [['label' => '', 'type' => 'text', 'options' => '']];
                                }

                                foreach ($fields as $i => $field) {
                                    echo '<tr>';
                                    echo '<td><input type="text" name="igny8_fixed_fields_config[' . $i . '][label]" value="' . esc_attr($field['label']) . '"></td>';
                                    echo '<td>
                                            <select name="igny8_fixed_fields_config[' . $i . '][type]">
                                              <option value="text" ' . selected($field['type'], 'text', false) . '>Text</option>
                                              <option value="select" ' . selected($field['type'], 'select', false) . '>Select</option>
                                              <option value="radio" ' . selected($field['type'], 'radio', false) . '>Radio</option>
                                            </select>
                                          </td>';
                                    echo '<td><input type="text" name="igny8_fixed_fields_config[' . $i . '][options]" value="' . esc_attr($field['options']) . '"></td>';
                                    echo '<td><button type="button" class="button igny8-remove-row">Remove</button></td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>

                        <!--== Add More Fields Button ==-->
                        <button type="button" id="igny8-add-row" class="button">Add Field</button>
                        <p class="description">You can add up to 6 fields. Options apply only to select/radio.</p>
                    </td>
                </tr>
            </table>

            <!--== Error Box Placeholder ==-->
            <div id="igny8-error-box" style="color:red; margin: 1em 0;"></div>

            <!--== Save Settings Button ==-->
            <?php submit_button('Save Settings'); ?>

        </form>
    </div>
    <?php
} 