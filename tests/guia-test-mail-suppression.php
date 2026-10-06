<?php
/** Temporary MU test helper. Installed only during local HTTP tests, then removed. */
defined('ABSPATH') || exit;
add_filter('pre_wp_mail', function ($return) {
    $post_id = (int) ($_POST['comment_post_ID'] ?? 0);
    return $post_id && get_post_meta($post_id, '_capao_guia_test_fixture', true) ? true : $return;
});
