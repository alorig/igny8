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
 * INTELLI module page
 */
function igny8_intelli_page() {
    ?>
    <div class="wrap">
        <h2>INTELLI Module</h2>
        
        <div class="igny8-tabs">
            <ul class="igny8-tab-nav">
                <li><a href="#intelli-overview">Overview</a></li>
                <li><a href="#intelli-queue">Regeneration Queue</a></li>
                <li><a href="#intelli-scheduling">Scheduling</a></li>
                <li><a href="#intelli-history">History & Rollback</a></li>
                <li><a href="#intelli-advanced">Advanced Settings</a></li>
            </ul>
            
            <!-- Overview Tab -->
            <div id="intelli-overview" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>INTELLI Module Overview</h3>
                    <div class="igny8-placeholder">
                        <h4>INTELLI Content Regeneration Engine</h4>
                        <p>This section will display INTELLI overview and quick actions.</p>
                    </div>
                </div>
            </div>
            
            <!-- Regeneration Queue Tab -->
            <div id="intelli-queue" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Regeneration Queue Management</h3>
                    <div class="igny8-placeholder">
                        <h4>Content Regeneration Queue</h4>
                        <p>This section will display the regeneration queue.</p>
                    </div>
                </div>
            </div>
            
            <!-- Scheduling Tab -->
            <div id="intelli-scheduling" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Content Scheduling</h3>
                    <div class="igny8-placeholder">
                        <h4>Automated Regeneration Scheduling</h4>
                        <p>This section will handle scheduling for regeneration.</p>
                    </div>
                </div>
            </div>
            
            <!-- History & Rollback Tab -->
            <div id="intelli-history" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Regeneration History</h3>
                    <div class="igny8-placeholder">
                        <h4>Content Version History</h4>
                        <p>This section will display history of regenerations and rollback options.</p>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Settings Tab -->
            <div id="intelli-advanced" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Advanced INTELLI Configuration</h3>
                    <div class="igny8-placeholder">
                        <h4>Advanced Settings</h4>
                        <p>This section will contain advanced INTELLI settings.</p>
                    </div>
                </div>
            </div>
        </div>
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
        
        <div class="igny8-tabs">
            <ul class="igny8-tab-nav">
                <li><a href="#loops-overview">Overview</a></li>
                <li><a href="#loops-keywords">Keyword Upload/Clustering</a></li>
                <li><a href="#loops-mapping">Cluster Mapping</a></li>
                <li><a href="#loops-linking">Internal Linking</a></li>
                <li><a href="#loops-advanced">Advanced Settings</a></li>
            </ul>
            
            <!-- Overview Tab -->
            <div id="loops-overview" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>LOOPS Module Overview</h3>
                    <div class="igny8-placeholder">
                        <h4>LOOPS Content Clustering Engine</h4>
                        <p>This section will show LOOPS overview.</p>
                    </div>
                </div>
            </div>
            
            <!-- Keyword Upload/Clustering Tab -->
            <div id="loops-keywords" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Keyword Management</h3>
                    <div class="igny8-placeholder">
                        <h4>Keyword Upload and Clustering</h4>
                        <p>This section will allow keyword upload and clustering.</p>
                    </div>
                </div>
            </div>
            
            <!-- Cluster Mapping Tab -->
            <div id="loops-mapping" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Cluster Mapping</h3>
                    <div class="igny8-placeholder">
                        <h4>Content Cluster Visualization</h4>
                        <p>This section will handle cluster mapping.</p>
                    </div>
                </div>
            </div>
            
            <!-- Internal Linking Tab -->
            <div id="loops-linking" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Internal Linking Strategy</h3>
                    <div class="igny8-placeholder">
                        <h4>Automated Internal Linking</h4>
                        <p>This section will manage internal linking strategies.</p>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Settings Tab -->
            <div id="loops-advanced" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Advanced LOOPS Configuration</h3>
                    <div class="igny8-placeholder">
                        <h4>Advanced Settings</h4>
                        <p>This section will contain advanced LOOPS settings.</p>
                    </div>
                </div>
            </div>
        </div>
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
        
        <div class="igny8-tabs">
            <ul class="igny8-tab-nav">
                <li><a href="#hive-overview">Overview</a></li>
                <li><a href="#hive-social">Social Posting</a></li>
                <li><a href="#hive-syndication">Blog Syndication</a></li>
                <li><a href="#hive-backlinks">Backlink Scheduling</a></li>
                <li><a href="#hive-advanced">Advanced Settings</a></li>
            </ul>
            
            <!-- Overview Tab -->
            <div id="hive-overview" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>HIVE Module Overview</h3>
                    <div class="igny8-placeholder">
                        <h4>HIVE Content Distribution Engine</h4>
                        <p>This section will display HIVE overview.</p>
                    </div>
                </div>
            </div>
            
            <!-- Social Posting Tab -->
            <div id="hive-social" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Social Media Distribution</h3>
                    <div class="igny8-placeholder">
                        <h4>Automated Social Posting</h4>
                        <p>This section will handle social posting setup.</p>
                    </div>
                </div>
            </div>
            
            <!-- Blog Syndication Tab -->
            <div id="hive-syndication" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Blog Syndication Management</h3>
                    <div class="igny8-placeholder">
                        <h4>Content Syndication Workflows</h4>
                        <p>This section will manage blog syndication workflows.</p>
                    </div>
                </div>
            </div>
            
            <!-- Backlink Scheduling Tab -->
            <div id="hive-backlinks" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Backlink Strategy</h3>
                    <div class="igny8-placeholder">
                        <h4>Automated Backlink Management</h4>
                        <p>This section will manage backlink scheduling.</p>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Settings Tab -->
            <div id="hive-advanced" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Advanced HIVE Configuration</h3>
                    <div class="igny8-placeholder">
                        <h4>Advanced Settings</h4>
                        <p>This section will contain advanced HIVE settings.</p>
                    </div>
                </div>
            </div>
        </div>
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
        
        <div class="igny8-tabs">
            <ul class="igny8-tab-nav">
                <li><a href="#skin-overview">Overview</a></li>
                <li><a href="#skin-theme">Theme Options</a></li>
                <li><a href="#skin-typography">Typography & Colors</a></li>
                <li><a href="#skin-advanced">Advanced Settings</a></li>
            </ul>
            
            <!-- Overview Tab -->
            <div id="skin-overview" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>SKIN Module Overview</h3>
                    <div class="igny8-placeholder">
                        <h4>SKIN Theme Customization Engine</h4>
                        <p>This section will display SKIN overview.</p>
                    </div>
                </div>
            </div>
            
            <!-- Theme Options Tab -->
            <div id="skin-theme" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Theme Customization</h3>
                    <div class="igny8-placeholder">
                        <h4>Theme Options and Layouts</h4>
                        <p>This section will manage theme options.</p>
                    </div>
                </div>
            </div>
            
            <!-- Typography & Colors Tab -->
            <div id="skin-typography" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Typography and Color Management</h3>
                    <div class="igny8-placeholder">
                        <h4>Design System Configuration</h4>
                        <p>This section will handle typography and color settings.</p>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Settings Tab -->
            <div id="skin-advanced" class="igny8-tab-content">
                <div class="igny8-tab-section">
                    <h3>Advanced SKIN Configuration</h3>
                    <div class="igny8-placeholder">
                        <h4>Advanced Settings</h4>
                        <p>This section will contain advanced SKIN settings.</p>
                    </div>
                </div>
            </div>
        </div>
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
 * Render the main Igny8 settings page with tabbed interface
 * This function outputs the complete HTML form for plugin configuration
 * including all form fields, validation, and user interface elements
 */
function igny8_settings_page() {
    ?>
    <div class="wrap">
        <h2>Igny8 Settings</h2>
        
        <div class="igny8-tabs">
            <ul class="igny8-tab-nav">
                <li><a href="#connections-api">Connections & API Keys</a></li>
                <li><a href="#styling-ui">Styling & UI Preferences</a></li>
                <li><a href="#diagnostics-dev">Diagnostics & Developer Tools</a></li>
            </ul>
            
            <form method="post" action="options.php" class="igny8-settings-form">
                <?php
                // == Register settings group and sections ==
                settings_fields('igny8_settings_group');
                do_settings_sections('igny8_settings_group');
                ?>

                <!-- Connections & API Keys Tab -->
                <div id="connections-api" class="igny8-tab-content">
                    <div class="igny8-tab-section">
                        <h3>OpenAI Configuration</h3>
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
                        </table>
                    </div>
                    
                    <div class="igny8-tab-section">
                        <h3>Content Moderation</h3>
                        <table class="form-table">
                            <tr>
                                <th>Use Moderation:</th>
                                <td><input type="checkbox" name="igny8_use_moderation" value="1" <?php checked(1, get_option('igny8_use_moderation'), true); ?>>
                                <p class="description">Enable OpenAI content moderation to filter inappropriate content.</p></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Styling & UI Preferences Tab -->
                <div id="styling-ui" class="igny8-tab-content">
                    <div class="igny8-tab-section">
                        <h3>Global UI Settings</h3>
                        <table class="form-table">
                            <tr>
                                <th>Teaser Text:</th>
                                <td><input type="text" name="igny8_teaser_text" value="<?php echo esc_attr(get_option('igny8_teaser_text', 'Want to read this as if it was written exclusively about you?')); ?>" size="80">
                                <p class="description">Default teaser text for personalization buttons (can be overridden by modules).</p></td>
                            </tr>
                            <tr>
                                <th>Button Background Color:</th>
                                <td><input type="text" name="igny8_button_color" value="<?php echo esc_attr(get_option('igny8_button_color', '#0073aa')); ?>" size="10">
                                <p class="description">Default button color for personalization buttons (can be overridden by modules).</p></td>
                            </tr>
                            <tr>
                                <th>Content Background Color:</th>
                                <td><input type="text" name="igny8_content_bg" value="<?php echo esc_attr(get_option('igny8_content_bg', '#f9f9f9')); ?>" size="10">
                                <p class="description">Default background color for personalized content (can be overridden by modules).</p></td>
                            </tr>
                            <tr>
                                <th>Custom CSS:</th>
                                <td><textarea name="igny8_custom_css" rows="5" cols="80"><?php echo esc_textarea(get_option('igny8_custom_css', '')); ?></textarea>
                                <p class="description">Global custom CSS for Igny8 components (can be overridden by modules).</p></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Diagnostics & Developer Tools Tab -->
                <div id="diagnostics-dev" class="igny8-tab-content">
                    <div class="igny8-tab-section">
                        <h3>Token Usage Counters</h3>
                        <div class="igny8-placeholder">
                            <h4>Token Usage Counters (coming soon)</h4>
                            <p>This section will display token usage statistics and cost tracking for all Igny8 modules.</p>
                        </div>
                    </div>
                    
                    <div class="igny8-tab-section">
                        <h3>Logs Export</h3>
                        <div class="igny8-placeholder">
                            <h4>Logs Export (coming soon)</h4>
                            <p>This section will provide debugging logs and export functionality for troubleshooting.</p>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="igny8-save-container">
                    <?php submit_button('Save Settings'); ?>
                </div>
            </form>
        </div>
    </div>
    <?php
} 