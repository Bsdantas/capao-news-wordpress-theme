<?php
/** CLI only. No HTTP endpoint is installed in WordPress. */
if (PHP_SAPI !== 'cli') {
    exit(1);
}
$root = $argv[1] ?? '';
if (!$root || !is_file($root . '/wp-load.php')) {
    throw new RuntimeException('Informe a pasta pública do LocalWP.');
}
$_SERVER['HTTP_HOST'] = 'capaonews.local';
$_SERVER['REQUEST_METHOD'] = 'GET';
require $root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$activation = activate_plugin('capao-news-guia/capao-news-guia.php');
if (is_wp_error($activation)) {
    throw new RuntimeException($activation->get_error_message());
}
echo wp_json_encode([
    'active' => is_plugin_active('capao-news-guia/capao-news-guia.php'),
    'guide_category' => (int) get_option('capao_guia_category_id'),
    'children' => get_terms(['taxonomy' => 'category', 'parent' => (int) get_option('capao_guia_category_id'), 'hide_empty' => false, 'fields' => 'names']),
    'comment_registration' => (bool) get_option('comment_registration'),
    'default_comment_status' => get_option('default_comment_status'),
    'close_comments_for_old_posts' => (bool) get_option('close_comments_for_old_posts'),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
