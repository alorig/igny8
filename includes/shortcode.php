<?php
// 🔒 Exit if accessed directly from outside WordPress
defined('ABSPATH') || exit;

// 🪄 Register shortcode [igny8] → maps to handler function below
add_shortcode('igny8', 'igny8_shortcode_handler');

// 🧠 Main Shortcode Handler Function
function igny8_shortcode_handler($atts) {

    // 🧼 Step 1: Normalize 'form-fields' → 'form_fields'
    // Supports both formats for better shortcode compatibility (e.g., form-fields="4,5")
    if (isset($atts['form-fields'])) {
        $atts['form_fields'] = $atts['form-fields'];
        unset($atts['form-fields']);
    }
	// ✅ Enable nested shortcodes in all attribute values

	foreach ($atts as $key => $value) {
    $atts[$key] = do_shortcode($value);
}

    // 📌 Step 1b: Store normalized field list (e.g., "4,5") if present
    $form_fields_value = $atts['form_fields'] ?? '';

    // 🧩 Step 2: Enqueue JS and CSS assets for frontend behavior + styling
    // - JS: Handles button click, loads form via AJAX, submits form
    // - CSS: Styles form layout, personalize button, output blocks
    wp_enqueue_script(
        'igny8-frontend',
        plugins_url('../assets/js/igny8.js', __FILE__),
        [],
        '2.2', // Cache version for JS
        true   // Load in footer
    );

    wp_enqueue_style(
        'igny8-style',
        plugins_url('../assets/css/igny8.css', __FILE__),
        [],
        '2.2'  // Cache version for CSS
    );

    // 📥 Step 3: Fetch context values for rendering and JS logic
    // - $post_id is used for contextual GPT prompt input
    // - $ajax_url is passed to JS for making AJAX calls
    // - $teaser is a text shown above the button
    // - $button_color is optional style pulled from admin
    $post_id = get_queried_object_id();

    $ajax_url = esc_url(admin_url('admin-ajax.php'));
    
    // Check if FLUX is enabled and use FLUX-specific settings if available
    $flux_status = get_option('igny8_flux_global_status', 'enabled');
    $post_type = get_post_type();
    $enabled_post_types = get_option('igny8_flux_enabled_post_types', []);
    
    if ($flux_status === 'enabled' && in_array($post_type, $enabled_post_types)) {
        // Use FLUX-specific settings
        $teaser = esc_html(get_option('igny8_flux_teaser_text', get_option('igny8_teaser_text', 'Want to read this as if it was written exclusively about you?')));
        $button_color = esc_attr(get_option('igny8_flux_button_color', get_option('igny8_button_color', '#0073aa')));
    } else {
        // Use global settings
        $teaser = esc_html(get_option('igny8_teaser_text', 'Want to read this as if it was written exclusively about you?'));
        $button_color = esc_attr(get_option('igny8_button_color', '#0073aa'));
    }

    // 🔁 Step 3b: (Re)store form field values explicitly for HTML generation
    $form_fields_value = $atts['form_fields'];

    // 🧱 Step 4: Start output buffering for HTML return
    ob_start();
?>

    <!-- 🔍 DEBUG: Show all shortcode attribute values -->
    <!-- Step 5: Render optional developer-facing debug box -->
    <!-- Displays all received shortcode attributes in a list -->
    
	
	<!-- <div style="background:#f9f9f9; padding:1em; border:1px solid #ddd; margin-bottom:1em;">
        <strong>Igny8 Shortcode Attributes:</strong>
        <ul style="margin:0; padding-left:1em;">
            <?php foreach ($atts as $k => $v): ?>
                <li><code><?php echo esc_html($k); ?></code>: <?php echo esc_html($v); ?></li>
            <?php endforeach; ?>
        </ul>
    </div> -->

    <!-- 🎯 Step 6: Render Teaser Line + Trigger Button Container -->
    <div id="igny8-trigger">
        <p class="igny8-teaser"><?php echo $teaser; ?></p>

       <?php
	   /*
        // 🧪 Step 5 (continued): Render second debug box, filtered
        // Only shows contextual values (not form_fields) for clarity
        echo '<div style="background:#f5f5f5; padding:0.5em; font-size:0.9em; border:1px dashed #999; margin-bottom:0.5em;">';
        echo '<strong>Shortcode Attributes Received:</strong><ul style="margin:0; padding-left:1em;">';
        foreach ($atts as $key => $val) {
            if (!in_array($key, ['form_fields', 'form-fields'])) {
                echo '<li><code>' . esc_html($key) . '</code> = "' . esc_html($val) . '"</li>';
            }
        }
        echo '</ul></div>';
        */?> 

        <!-- 🚀 Step 7: Render Personalize Button -->
        <!-- JS reads data-* attributes to initialize form load and context -->
        <button class="button" id="igny8-launch"
            data-ajax-url="<?php echo $ajax_url; ?>"
            data-post-id="<?php echo $post_id; ?>"
            data-form-fields="<?php echo esc_attr($form_fields_value); ?>"
            <?php
            // 🌐 Step 7b: Render all additional shortcode attributes as data-* props
            // Used for hidden context injection (e.g., data-vehicle, data-brand)
            foreach ($atts as $key => $val) {
                if (!in_array($key, ['form_fields', 'form-fields'])) {
                    echo ' data-' . esc_attr($key) . '="' . esc_attr($val) . '"';
                }
            }
            ?>
        >Personalize</button>
    </div>
	
	<!-- 🔒 Step 7b: Inject admin-defined context (hidden) -->
<?php
// Check if FLUX is enabled and use FLUX-specific context if available
if ($flux_status === 'enabled' && in_array($post_type, $enabled_post_types)) {
    $context_raw = get_option('igny8_flux_context_source', get_option('igny8_context_source', ''));
} else {
    $context_raw = get_option('igny8_context_source', '');
}

if (!empty($context_raw)) {
    echo '<div id="igny8-context" style="display:none;">';
    echo do_shortcode($context_raw); // evaluate here at output time
    echo '</div>';
}
?>


    <!-- 🧪 Step 8: Output placeholder for form + GPT result -->
    <!-- JS will populate this div on button click and after GPT response -->
    <div id="igny8-output"></div>

    <?php
    // 🔚 Step 9: End output buffering and return entire generated block
    return ob_get_clean();
}
