<?php
// 🔒 Exit if accessed directly from outside WordPress
defined('ABSPATH') || exit;

function igny8_build_combined_content() {
    $include_content = get_option('igny8_include_post_content') === '1';
    $include_context = get_option('igny8_include_page_context') === '1';

    $final_content = '';

    // ✅ Use PageContent from form if available
    if ($include_content && !empty($_POST['PageContent'])) {
        $final_content .= "[SOURCE:PageContent from form]\n\n";
        $final_content .= trim(sanitize_text_field($_POST['PageContent']));
    }

    // ✅ Fallback to raw post content or term description
    if ($include_content && empty($_POST['PageContent'])) {
        $queried = get_queried_object();

        if ($queried instanceof WP_Post) {
            // 🎯 Post/page/product — use post content
            $raw_content = get_post_field('post_content', $queried->ID);
            if (!empty($raw_content)) {
                $final_content .= "[SOURCE:Post Content]\n\n";
                $final_content .= wp_trim_words(strip_tags($raw_content), 300, '...');
            }

        } elseif (isset($queried->description) && !empty($queried->description)) {
            // 🏷️ Archive (term) — use term description
            $final_content .= "[SOURCE:Term Description]\n\n";
            $final_content .= wp_trim_words(strip_tags($queried->description), 300, '...');
        }
    }

    return trim($final_content) ?: 'No content available.';
}


/**
 * 🛡️ Checks content for moderation violations using OpenAI's moderation API
 *
 * @param string $text     The text to check for policy violations
 * @param string $api_key  Your OpenAI secret API key
 *
 * @return array {
 *     @type bool   $flagged    Whether the content was flagged
 *     @type array  $categories List of moderation categories (e.g. hate, violence)
 *     @type string $error      If error occurs, the message is returned in this key
 * }
 */
function igny8_check_moderation($text, $api_key) {
    $res = wp_remote_post('https://api.openai.com/v1/moderations', [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode(['input' => $text]),
        'timeout' => 20,
    ]);

    if (is_wp_error($res)) {
        return ['flagged' => false, 'error' => $res->get_error_message()];
    }

    $body = json_decode(wp_remote_retrieve_body($res), true);
    return [
        'flagged'    => $body['results'][0]['flagged'] ?? false,
        'categories' => $body['results'][0]['categories'] ?? [],
    ];
}

/**
 * 🔌 Tests whether the provided OpenAI API key is valid and working
 *
 * @param string $api_key  OpenAI secret API key
 * @return true|string     Returns true on success, or error message on failure
 */
function igny8_test_connection($api_key) {
    $res = wp_remote_post('https://api.openai.com/v1/moderations', [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode(['input' => 'test']),
        'timeout' => 10,
    ]);

    if (is_wp_error($res)) {
        return $res->get_error_message();
    }

    $code = wp_remote_retrieve_response_code($res);
    return ($code >= 200 && $code < 300)
        ? true
        : 'HTTP ' . $code . ' – ' . wp_remote_retrieve_body($res);
}

function igny8_call_openai($prompt, $api_key, $model) {
    $args = [
        'body' => json_encode([
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.7,
        ]),
        'headers' => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'timeout' => 60,
    ];

    $res = wp_remote_post('https://api.openai.com/v1/chat/completions', $args);

    if (is_wp_error($res)) {
        return 'Error: ' . $res->get_error_message();
    }

    $body = json_decode(wp_remote_retrieve_body($res), true);
    return $body['choices'][0]['message']['content'] ?? 'No response.';
}
