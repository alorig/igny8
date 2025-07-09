<?php
defined('ABSPATH') || exit;

// Register AJAX endpoints
add_action('wp_ajax_igny8_get_fields', 'igny8_ajax_get_fields');
add_action('wp_ajax_nopriv_igny8_get_fields', 'igny8_ajax_get_fields');

add_action('wp_ajax_igny8_generate_custom', 'igny8_ajax_generate_custom');
add_action('wp_ajax_nopriv_igny8_generate_custom', 'igny8_ajax_generate_custom');

function igny8_ajax_get_fields() {
    // 🔹 Step 1: Load current field mode (fixed or dynamic)
    $mode = get_option('igny8_field_mode', 'dynamic');

    // 🔹 Step 2: Fixed mode logic (render admin-defined fields)
    if ($mode === 'fixed') {
        // 🧱 Load saved field config from WP options table
        $fields = get_option('igny8_fixed_fields_config', []);

        // 🎯 Get requested field IDs from shortcode (form_fields="1,2")
        $form_field_ids = array_map('intval', explode(',', sanitize_text_field($_GET['form_fields'] ?? '')));

        // 🧩 Track which fields are rendered (slugs)
        $visible_slugs = [];

        // 🚫 Bail out if config is missing or labels are empty
        if (empty($fields) || count(array_filter($fields, fn($f) => !empty($f['label']))) === 0) {
            echo '<div style="color:red;">❌ Incomplete data. Please personalize before proceeding.</div>';
            wp_die();
        }

        // 🔘 Begin form rendering
        echo '<form id="igny8-form">';
        echo "<!-- DEBUG: Showing fields = " . implode(',', $form_field_ids) . " -->";

        // 🔁 Loop through fixed fields and render those matched by form_fields
        foreach ($fields as $index => $field) {
            if (!empty($form_field_ids) && !in_array($index + 1, $form_field_ids)) continue;

            $label = esc_html($field['label'] ?? '');
            $name = esc_attr($label);
            $slug = sanitize_title($label); // Slug used to match against shortcode keys
            $visible_slugs[] = $slug;

            $type = $field['type'] ?? 'text';

            // ✅ Override field options if values were passed via shortcode attribute (e.g. budget="Low,High")
            $shortcode_value = $_GET[$slug] ?? '';
            if (!empty($shortcode_value)) {
                $options = array_filter(array_map('trim', explode(',', $shortcode_value)));
            } else {
                $options = array_filter(array_map('trim', explode(',', $field['options'] ?? '')));
            }

            // 🎨 Render field based on type
            if ($type === 'select') {
                echo "<label for='$name'>$label:</label><select name='$name'>";
                foreach ($options as $option) {
                    echo "<option value='" . esc_attr($option) . "'>" . esc_html($option) . "</option>";
                }
                echo "</select>";

            } elseif ($type === 'radio') {
                echo "<label>$label:</label><br>";
                foreach ($options as $option) {
                    echo "<label><input type='radio' name='$name' value='" . esc_attr($option) . "'> " . esc_html($option) . "</label> ";
                }
                

            } else {
    $placeholder = esc_attr($field['options'] ?? ''); // Use 'options' field as placeholder if it's a text field
    echo "<label for='$name'>$label:</label>";
    echo "<input type='text' name='$name' placeholder='$placeholder'>";
}
        }

        // 🔒 Step 3: Inject hidden fields for context (shortcode fields not shown in form_fields)
        foreach ($fields as $field) {
            $label = $field['label'] ?? '';
            $slug  = sanitize_title($label);

            if (!in_array($slug, $visible_slugs) && isset($_GET[$slug]) && $_GET[$slug] !== '') {
    $raw_value = $_GET[$slug];
    $resolved_value = do_shortcode($raw_value);
    
	echo "<input type='hidden' name='" . esc_attr($label) . "' value='" . esc_attr($resolved_value) . "' />";
}

        }
		echo '<input type="hidden" name="PageContent" id="PageContent" value="">';
		

        // 🔘 Final submit button and output placeholder
        echo '<button type="submit" class="button">Personalize</button>';
        echo '</form><div id="igny8-generated-content"></div>';

        wp_die(); // ✅ Kill AJAX properly
    }

    // 🟡 If mode is 'dynamic' (handled below this block), this logic does not run


// 🔹 DYNAMIC MODE: GPT-based field detection and rendering
// This block runs only if the admin has selected 'dynamic' mode (instead of 'fixed')

$post_id = intval($_GET['post_id']); // 🎯 Post ID is passed via data-post-id for context
$api_key = get_option('igny8_api_key');
$model = get_option('igny8_model', 'gpt-3.5-turbo');
$scope = get_option('igny8_input_scope', 'title'); // e.g., 'title', 'content', etc.
$prompt_template = get_option('igny8_detection_prompt');

// 🔁 STEP 1: Load content from post based on scope
$content = get_igny8_content_scope($post_id, $scope); // returns title/content/body
$prompt = str_replace('[CONTENT]', $content, $prompt_template); // inject content into prompt

// 🔁 STEP 2: Check for existing field structure in cache
$cached = get_post_meta($post_id, '_igny8_fields', true);

if (is_array($cached)) {
    $fields = $cached; // 🧠 Use cached GPT result if available
} else {
    // ⚙️ STEP 3: Call OpenAI API for dynamic field structure
    $response = igny8_call_openai($prompt, $api_key, $model);
    $fields = json_decode($response, true);

    // 🛑 Error handling for invalid GPT output
    if (!is_array($fields)) {
        echo "<strong>❌ Invalid GPT response:</strong><br><pre>" . esc_html($response) . "</pre>";
        wp_die();
    }

    // 💾 Cache GPT result for this post
    update_post_meta($post_id, '_igny8_fields', $fields);
}

// 🔢 STEP 4: Extract fields from GPT result
$fieldset = $fields['fields'] ?? [];

// 🔘 Start rendering the dynamic form
echo '<form id="igny8-form">';

// 🌐 STEP 5: Auto-detect user's city using IP API (client-side JS)
echo "<script>
    async function getCity() {
        try {
            const res = await fetch('http://ip-api.com/json');
            const data = await res.json();
            return data.city || '';
        } catch { return ''; }
    }
    document.addEventListener('DOMContentLoaded', async () => {
        const city = await getCity();
        const cityInputs = document.querySelectorAll('[name=\"City\"], [name=\"Location\"]');
        cityInputs.forEach(el => el.value = city);
    });
</script>";

// 🔁 STEP 6: Render each field returned by GPT
foreach ($fieldset as $field) {
    $label = esc_html($field['label'] ?? '');
    $name = esc_attr($label);

    // 📦 Handle select fields
    if (($field['type'] ?? '') === 'select') {
        echo "<label for='$name'>$label:</label><select name='$name'>";
        foreach ($field['options'] ?? [] as $option) {
            echo "<option value='" . esc_attr($option) . "'>" . esc_html($option) . "</option>";
        }
        echo "</select><br><br>";

    } else {
        // ✍️ Render text inputs with examples as placeholders
        $placeholder = isset($field['examples']) ? implode(', ', $field['examples']) : '';
        echo "<label for='$name'>$label:</label><input type='text' name='$name' placeholder='" . esc_attr($placeholder) . "' /><br><br>";
    }
}

// 🔘 Final submit button and container for GPT output
echo '<button type="submit" class="button">Personalize</button>';
echo '</form><div id="igny8-generated-content"></div>';

// ✅ End the AJAX response cleanly
wp_die();
}

function igny8_ajax_generate_custom() {
    // 🔹 Step 1: Get context for the current post
    $post_id = intval($_GET['post_id']);

    // 🔹 Step 2: Load OpenAI configuration from admin
    $api_key = get_option('igny8_api_key');
    $model = get_option('igny8_model', 'gpt-3.5-turbo');
    $length = get_option('igny8_content_length', '300'); // e.g. 100 words, 300 words, or 'full'
    $prompt_template = get_option('igny8_rewrite_prompt', '');

 // 🔹 Step 3: Fully restore page context so shortcodes behave exactly like page view
if (!empty($post_id)) {
    global $post, $wp_query;

    $post = get_post($post_id);
    setup_postdata($post);

    // 🧠 Simulate global query context (so get_queried_object() works)
    $wp_query = new WP_Query(['p' => $post_id, 'post_type' => get_post_type($post_id)]);
    $GLOBALS['wp_query'] = $wp_query;
    $GLOBALS['wp_the_query'] = $wp_query;
}


// 🔹 Step 3.1: Build dynamic content from page + context shortcode
$content = igny8_build_combined_content();


    if ($length !== 'full') {
        $content = wp_trim_words($content, intval($length)); // Trim to target word count
    }

    // 🔹 Step 4: Sanitize user form input
    $input = [];
    foreach ($_POST as $k => $v) {
        $input[$k] = sanitize_text_field($v); // Clean all submitted form fields
    }

// 🔹 Step 5: Build final GPT prompt (clean, readable, GPT-optimized)
require_once plugin_dir_path(__FILE__) . 'utils.php';

$lines = [];
foreach ($input as $key => $val) {
    if (!is_string($val)) continue;

    $cleaned = igny8_format_field($val);

    // Optional: format label as "Road Conditions" instead of "road_conditions"
    $label = ucwords(str_replace(['_', '-'], ' ', $key));

    $lines[] = "- {$label}: {$cleaned}";
}

$user_input = implode("\n", $lines);
$final_prompt = str_replace(['[INPUTS]', '[CONTENT]'], [$user_input, $content], $prompt_template);


    // 🔹 Step 6: Call OpenAI with the final prompt
    $response = igny8_call_openai($final_prompt, $api_key, $model);

    // 🔒 Step 7: Optional moderation check (e.g. for NSFW content, abuse, etc.)
   if (get_option('igny8_use_moderation')) {
    $moderation = igny8_check_moderation($response, $api_key);
    if (!empty($moderation['flagged'])) {
        wp_reset_postdata(); // ✅ prevent global post pollution
        echo '<div style="color:red;">⚠️ Content flagged by moderation system.</div>';
        wp_die();
    }
}

    

    // ✅ Step 8: Output the GPT-generated response inside styled div
	wp_reset_postdata(); // ✅ always clean up after setup_postdata
    echo '<div class="igny8-final-content">' . wp_kses_post($response) . '</div>';
    wp_die(); // Clean AJAX exit
}
// ✅ Add missing Test API endpoint
add_action('wp_ajax_igny8_test_api', 'igny8_test_api_callback');

function igny8_test_api_callback() {
    $api_key = get_option('igny8_api_key');

    // Use your existing utility function from openai.php
    $result = igny8_test_connection($api_key);

    if ($result === true) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => $result]);
    }
}
