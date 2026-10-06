<?php
/**
 * Plugin Name: Capão News — Guia
 * Description: Informações de estabelecimentos e avaliações moderadas com comentários nativos.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Bruno Dantas
 * Text Domain: capao-news-guia
 */

defined('ABSPATH') || exit;

function capao_guia_activate(): void
{
    $parent = capao_guia_ensure_category('Guia Capão', 'guia-capao');
    update_option('capao_guia_category_id', $parent, false);
    foreach (['Restaurantes' => 'restaurantes', 'Bares' => 'bares', 'Sorveterias' => 'sorveterias'] as $name => $slug) {
        capao_guia_ensure_category($name, $slug, $parent);
    }
    // Change only the Bairros item in the currently assigned primary menu.
    $locations = get_nav_menu_locations();
    if (empty($locations['primary'])) {
        return;
    }
    foreach (wp_get_nav_menu_items($locations['primary']) ?: [] as $item) {
        $object = $item->type === 'taxonomy' ? get_term($item->object_id, 'category') : get_post($item->object_id);
        $slug = $object && !is_wp_error($object) ? ($object->slug ?? $object->post_name ?? '') : '';
        if (sanitize_title($item->title) !== 'bairros' && $slug !== 'bairros') {
            continue;
        }
        wp_update_nav_menu_item($locations['primary'], $item->ID, [
            'menu-item-title' => 'Guia Capão', 'menu-item-object' => 'category',
            'menu-item-object-id' => $parent, 'menu-item-type' => 'taxonomy',
            'menu-item-parent-id' => $item->menu_item_parent, 'menu-item-position' => $item->menu_order,
            'menu-item-status' => $item->post_status, 'menu-item-target' => $item->target,
            'menu-item-classes' => implode(' ', $item->classes), 'menu-item-xfn' => $item->xfn,
            'menu-item-description' => $item->description, 'menu-item-attr-title' => $item->attr_title,
        ]);
    }
}
register_activation_hook(__FILE__, 'capao_guia_activate');

function capao_guia_ensure_category(string $name, string $slug, int $parent = 0): int
{
    $term = get_term_by('slug', $slug, 'category') ?: get_term_by('name', $name, 'category');
    if ($term) {
        if ($parent && (int) $term->parent !== $parent) {
            $result = wp_update_term($term->term_id, 'category', ['parent' => $parent]);
            if (is_wp_error($result)) {
                wp_die(esc_html($result->get_error_message()));
            }
        }
        return (int) $term->term_id;
    }
    $result = wp_insert_term($name, 'category', ['slug' => $slug, 'parent' => $parent]);
    if (is_wp_error($result)) {
        wp_die(esc_html($result->get_error_message()));
    }
    return (int) $result['term_id'];
}

function capao_guia_is_post(int $post_id): bool
{
    if (get_post_type($post_id) !== 'post') {
        return false;
    }
    $root = (int) get_option('capao_guia_category_id');
    if (!$root) {
        return false;
    }
    foreach (wp_get_post_categories($post_id) as $category) {
        if ((int) $category === $root || in_array($root, array_map('intval', get_ancestors($category, 'category')), true)) {
            return true;
        }
    }
    return false;
}

function capao_guia_fields(): array
{
    return [
        'address' => ['label' => 'Endereço', 'type' => 'text'],
        'hours' => ['label' => 'Horário de funcionamento', 'type' => 'textarea'],
        'phone' => ['label' => 'Telefone', 'type' => 'text'],
        'whatsapp' => ['label' => 'Link de WhatsApp', 'type' => 'url'],
        'website' => ['label' => 'Site ou rede social', 'type' => 'url'],
    ];
}

add_action('add_meta_boxes_post', function ($post): void {
    add_meta_box('capao-guia-details', 'Guia Capão — informações práticas', 'capao_guia_post_box', 'post', 'normal', 'default');
});

function capao_guia_post_box($post): void
{
    wp_nonce_field('capao_guia_save_post', 'capao_guia_post_nonce');
    echo '<p>Campos opcionais. Para exibi-los, publique a matéria em Guia Capão ou em uma de suas subcategorias.</p>';
    foreach (capao_guia_fields() as $key => $field) {
        $name = 'capao_guia_' . $key;
        $value = (string) get_post_meta($post->ID, $name, true);
        echo '<p><label for="' . esc_attr($name) . '"><strong>' . esc_html($field['label']) . '</strong></label><br>';
        if ($field['type'] === 'textarea') {
            echo '<textarea class="widefat" rows="3" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input class="widefat" type="' . esc_attr($field['type']) . '" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '">';
        }
        echo '</p>';
    }
}

add_action('save_post_post', function (int $post_id): void {
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)
        || !current_user_can('edit_post', $post_id)
        || empty($_POST['capao_guia_post_nonce']) || !is_string($_POST['capao_guia_post_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['capao_guia_post_nonce'])), 'capao_guia_save_post')) {
        return;
    }
    foreach (capao_guia_fields() as $key => $field) {
        $name = 'capao_guia_' . $key;
        $value = isset($_POST[$name]) && is_string($_POST[$name]) ? wp_unslash($_POST[$name]) : '';
        $value = $field['type'] === 'url' ? esc_url_raw($value, ['http', 'https']) : sanitize_textarea_field($value);
        if ($value === '') {
            delete_post_meta($post_id, $name);
        } else {
            update_post_meta($post_id, $name, $value);
        }
    }
});

function capao_guia_valid_rating($value): bool
{
    return (is_int($value) || is_string($value)) && preg_match('/^[1-5]$/D', (string) $value) === 1;
}

// wp_new_comment still performs native duplicate, flood and spam checks.
add_filter('preprocess_comment', function (array $data): array {
    $post_id = (int) ($data['comment_post_ID'] ?? 0);
    if (!capao_guia_is_post($post_id) || !in_array($data['comment_type'] ?? '', ['', 'comment'], true)) {
        return $data;
    }
    $nonce = $_POST['capao_guia_review_nonce'] ?? '';
    $rating = $_POST['capao_guia_rating'] ?? null;
    if (!is_string($nonce) || !wp_verify_nonce(sanitize_text_field(wp_unslash($nonce)), 'capao_guia_review_' . $post_id)) {
        wp_die('O formulário expirou. Recarregue a matéria e envie novamente.', 'Avaliação não enviada', ['response' => 403, 'back_link' => true]);
    }
    if (!capao_guia_valid_rating($rating)) {
        wp_die('Selecione uma nota de 1 a 5 estrelas.', 'Avaliação não enviada', ['response' => 400, 'back_link' => true]);
    }
    if (trim((string) ($data['comment_author'] ?? '')) === ''
        || !is_email($data['comment_author_email'] ?? '')
        || trim(wp_strip_all_tags((string) ($data['comment_content'] ?? ''))) === '') {
        wp_die('Informe nome, e-mail válido e comentário.', 'Avaliação não enviada', ['response' => 400, 'back_link' => true]);
    }
    if (!isset($data['comment_meta']) || !is_array($data['comment_meta'])) {
        $data['comment_meta'] = [];
    }
    $data['comment_meta']['capao_guia_rating'] = (int) $rating;
    return $data;
});

add_filter('pre_comment_approved', function ($approved, array $data) {
    if (is_wp_error($approved) || in_array($approved, ['spam', 'trash'], true)
        || !capao_guia_is_post((int) ($data['comment_post_ID'] ?? 0))
        || !in_array($data['comment_type'] ?? '', ['', 'comment'], true)) {
        return $approved;
    }
    return 0;
}, 100, 2);

function capao_guia_review_form_fields(): void
{
    $post_id = (int) get_the_ID();
    if (!capao_guia_is_post($post_id)) {
        return;
    }
    wp_nonce_field('capao_guia_review_' . $post_id, 'capao_guia_review_nonce', false);
    do_action('capao_guia_rating_form', $post_id);
}
add_action('comment_form_logged_in_after', 'capao_guia_review_form_fields');
add_action('comment_form_after_fields', 'capao_guia_review_form_fields');

add_filter('comments_template_query_args', function (array $args): array {
    if (capao_guia_is_post((int) get_the_ID())) {
        $args['include_unapproved'] = [];
        $args['status'] = 'approve';
    }
    return $args;
});

add_filter('comments_template_top_level_query_args', function (array $args): array {
    if (capao_guia_is_post((int) get_the_ID())) {
        $args['include_unapproved'] = [];
        $args['status'] = 'approve';
    }
    return $args;
});

function capao_guia_rating_summary(int $post_id): array
{
    global $wpdb;
    // No stored aggregate: status/metadata changes are reflected on the next read.
    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT COUNT(*) AS total, AVG(r.rating) AS average FROM (
            SELECT c.comment_ID, MAX(CAST(m.meta_value AS UNSIGNED)) AS rating
            FROM {$wpdb->comments} c INNER JOIN {$wpdb->commentmeta} m ON c.comment_ID = m.comment_id
            WHERE c.comment_post_ID = %d AND c.comment_approved = '1'
              AND c.comment_type IN ('', 'comment') AND m.meta_key = 'capao_guia_rating'
              AND CHAR_LENGTH(m.meta_value) = 1 AND m.meta_value REGEXP '^[1-5]$' GROUP BY c.comment_ID
        ) r",
        $post_id
    ));
    return ['count' => (int) ($row->total ?? 0), 'average' => (float) ($row->average ?? 0)];
}

add_action('add_meta_boxes_comment', function ($comment): void {
    if (capao_guia_is_post((int) $comment->comment_post_ID)) {
        add_meta_box('capao-guia-rating', 'Guia Capão — nota', 'capao_guia_comment_box', 'comment', 'normal', 'default');
    }
});

function capao_guia_comment_box($comment): void
{
    wp_nonce_field('capao_guia_edit_rating_' . $comment->comment_ID, 'capao_guia_rating_nonce');
    $rating = get_comment_meta($comment->comment_ID, 'capao_guia_rating', true);
    echo '<label for="capao-guia-admin-rating">Nota da avaliação</label> <select id="capao-guia-admin-rating" name="capao_guia_admin_rating"><option value="">Sem nota</option>';
    for ($i = 1; $i <= 5; $i++) {
        echo '<option value="' . esc_attr((string) $i) . '" ' . selected((string) $rating, (string) $i, false) . '>' . esc_html($i . ' / 5') . '</option>';
    }
    echo '</select><p>Comentários sem nota permanecem visíveis e não entram na média.</p>';
}

add_action('edit_comment', function (int $comment_id): void {
    $comment = get_comment($comment_id);
    if (!$comment || !capao_guia_is_post((int) $comment->comment_post_ID)
        || !current_user_can('moderate_comments') || !current_user_can('edit_comment', $comment_id)
        || !isset($_POST['capao_guia_admin_rating'], $_POST['capao_guia_rating_nonce'])
        || !is_string($_POST['capao_guia_admin_rating']) || !is_string($_POST['capao_guia_rating_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['capao_guia_rating_nonce'])), 'capao_guia_edit_rating_' . $comment_id)) {
        return;
    }
    $rating = wp_unslash($_POST['capao_guia_admin_rating']);
    if ($rating === '') {
        delete_comment_meta($comment_id, 'capao_guia_rating');
    } elseif (capao_guia_valid_rating($rating)) {
        update_comment_meta($comment_id, 'capao_guia_rating', (int) $rating);
    } else {
        wp_die('Nota inválida. Use um número de 1 a 5.', 'Nota não alterada', ['response' => 400, 'back_link' => true]);
    }
});

add_filter('manage_edit-comments_columns', function (array $columns): array {
    $columns['capao_guia_rating'] = 'Nota do Guia';
    return $columns;
});
add_action('manage_comments_custom_column', function (string $column, int $comment_id): void {
    if ($column !== 'capao_guia_rating') {
        return;
    }
    $comment = get_comment($comment_id);
    $rating = get_comment_meta($comment_id, 'capao_guia_rating', true);
    echo $comment && capao_guia_is_post((int) $comment->comment_post_ID) && capao_guia_valid_rating($rating) ? esc_html($rating . ' / 5') : '—';
}, 10, 2);

add_action('admin_notices', function (): void {
    if (!current_user_can('manage_options') || get_current_screen()->id !== 'options-discussion') {
        return;
    }
    if (get_option('comment_registration')) {
        echo '<div class="notice notice-info"><p>Guia Capão: para receber avaliações sem conta, desmarque “Os usuários devem estar registrados e conectados para comentar”.</p></div>';
    }
    echo '<div class="notice notice-info"><p>Guia Capão: permita comentários nas matérias do guia. Avaliações novas serão moderadas, independentemente da moderação das demais matérias.</p></div>';
});
