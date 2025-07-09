<?php
defined('ABSPATH') || exit;

/**
 * Get content from a post based on selected scope.
 *
 * @param int $post_id
 * @param string $scope 'title', '300', or 'full'
 * @return string
 */
function get_igny8_content_scope($post_id, $scope = '300') {    if (is_tax() || is_category() || is_tag()) {        $term = get_queried_object();        if ($term && isset($term->description) && !empty($term->description)) {            return strip_tags($term->description);        }    }    if ($scope === 'title') {        return get_the_title($post_id);    }    $post = get_post($post_id);    if (!$post instanceof WP_Post) return '';    $post_type = get_post_type($post);    if (in_array($post_type, ['product', 'post', 'page'])) {        $content = strip_tags($post->post_content);    } else {        return '';    }    if ($scope === '300') {        return wp_trim_words($content, 300, '');    }    return $content;}

/**
 * Sanitize all form inputs before passing to GPT.
 *
 * @param array $raw_data
 * @return array
 */
function igny8_sanitize_inputs($raw_data) {
    $clean = [];
    foreach ($raw_data as $key => $value) {
        $clean[sanitize_text_field($key)] = sanitize_text_field($value);
    }
    return $clean;
}

/**
 * Inject inputs and content into the prompt template.
 *
 * @param string $template
 * @param array $inputs
 * @param string $content
 * @return string
 */
function igny8_build_prompt($template, $inputs, $content) {
    $json_inputs = json_encode($inputs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return str_replace(['[INPUTS]', '[CONTENT]'], [$json_inputs, $content], $template);
}
function igny8_format_field($value) {    if (is_array($value)) {        return array_map('igny8_format_field', $value);    }    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');    $value = str_replace(["\r\n", "\r"], "\n", $value);    $value = preg_replace("/\n{2,}/", "\n", $value);    return trim(mb_convert_encoding($value, 'UTF-8', 'UTF-8'));}