<?php
/** Run only against an explicitly supplied LocalWP installation. */
if (PHP_SAPI !== 'cli') {
    exit(1);
}
$root = $argv[1] ?? '';
$mode = $argv[2] ?? 'prepare';
$manifest_path = $argv[3] ?? sys_get_temp_dir() . '/capao-guia-test.json';
if (!$root || !is_file($root . '/wp-load.php')) {
    throw new RuntimeException('Informe a pasta pública do LocalWP.');
}
$_SERVER['HTTP_HOST'] = 'capaonews.local';
$_SERVER['REQUEST_METHOD'] = 'GET';
require $root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
add_filter('pre_wp_mail', '__return_true'); // Never send test notifications.
$plugin = 'capao-news-guia/capao-news-guia.php';
if ($mode === 'deactivate') {
    deactivate_plugins($plugin);
    echo "Plugin temporariamente desativado para teste HTTP.\n";
    exit;
}
if ($mode === 'reactivate') {
    $result = activate_plugin($plugin);
    if (is_wp_error($result)) {
        throw new RuntimeException($result->get_error_message());
    }
    echo "Plugin reativado.\n";
    exit;
}
if ($mode === 'cleanup') {
    $manifest = json_decode(file_get_contents($manifest_path), true);
    foreach ($manifest['post_ids'] as $id) {
        if (get_post_meta($id, '_capao_guia_test_fixture', true) !== $manifest['token']) {
            throw new RuntimeException('Registro de teste não corresponde; limpeza interrompida.');
        }
        wp_delete_post($id, true);
    }
    echo 'Removidas apenas ' . count($manifest['post_ids']) . " matérias temporárias e seus comentários.\n";
    exit;
}
if ($mode === 'verify-http') {
    $manifest = json_decode(file_get_contents($manifest_path), true);
    $comments = get_comments(['post_id' => $manifest['guide_post_id'], 'author_email' => 'http-guia@example.test', 'status' => 'all']);
    if (!$comments) {
        throw new RuntimeException('Comentário enviado por HTTP não encontrado.');
    }
    foreach ($comments as $comment) {
        if ((string) $comment->comment_approved !== '0' || (int) get_comment_meta($comment->comment_ID, 'capao_guia_rating', true) !== 5) {
            throw new RuntimeException('Avaliação HTTP não está pendente com a nota esperada.');
        }
    }
    $summary = capao_guia_rating_summary($manifest['guide_post_id']);
    if ($summary !== ['count' => 1, 'average' => 4.0]) {
        throw new RuntimeException('Avaliação pendente entrou indevidamente na média.');
    }
    $ordinary_post = (int) $manifest['post_ids'][1];
    wp_update_post(['ID' => $ordinary_post, 'post_status' => 'publish']);
    $_POST = [];
    $ordinary_comment = wp_new_comment([
        'comment_post_ID' => $ordinary_post, 'comment_author' => 'Comentário comum de teste',
        'comment_author_email' => 'ordinary-guia@example.test', 'comment_author_url' => '',
        'comment_author_IP' => '192.0.2.11', 'comment_content' => 'Comentário nativo fora do Guia ' . wp_generate_uuid4(),
        'comment_type' => 'comment',
    ], true);
    if (is_wp_error($ordinary_comment) || get_comment_meta($ordinary_comment, 'capao_guia_rating', true) !== '') {
        throw new RuntimeException('Comentário comum fora do Guia falhou.');
    }
    echo wp_json_encode(['http_reviews_pending' => count($comments), 'summary' => $summary, 'ordinary_comment_without_rating' => true], JSON_PRETTY_PRINT);
    exit;
}
if (!is_plugin_active($plugin)) {
    throw new RuntimeException('Ative o plugin antes de executar.');
}
$checks = [];
function capao_guia_test_assert(bool $value, string $label): void
{
    global $checks;
    $checks[$label] = $value;
    if (!$value) {
        throw new RuntimeException('Falhou: ' . $label);
    }
}
$categories_before = wp_count_terms(['taxonomy' => 'category', 'hide_empty' => false]);
capao_guia_activate();
capao_guia_activate();
capao_guia_test_assert($categories_before === wp_count_terms(['taxonomy' => 'category', 'hide_empty' => false]), 'Ativação idempotente sem categorias duplicadas');
$root_id = (int) get_option('capao_guia_category_id');
$restaurant = get_term_by('slug', 'restaurantes', 'category');
capao_guia_test_assert((int) $restaurant->parent === $root_id, 'Restaurantes é subcategoria do guia');
$admins = get_users(['role' => 'administrator', 'number' => 1]);
if (!$admins) {
    throw new RuntimeException('Administrador não encontrado para testar permissões.');
}
$token = wp_generate_uuid4();
$post_id = wp_insert_post([
    'post_title' => '[TESTE TEMPORÁRIO] Restaurante do Guia Capão',
    'post_content' => '<p>Matéria temporária para conferir informações práticas e avaliações do Guia Capão.</p>',
    'post_excerpt' => 'Uma experiência de teste do guia, com endereço, horário e avaliações moderadas.',
    'post_status' => 'publish', 'post_type' => 'post', 'post_author' => $admins[0]->ID,
    'post_category' => [$restaurant->term_id], 'comment_status' => 'open',
    'meta_input' => ['_capao_guia_test_fixture' => $token],
], true);
if (is_wp_error($post_id)) {
    throw new RuntimeException($post_id->get_error_message());
}
$manifest = ['token' => $token, 'post_ids' => [$post_id], 'guide_post_id' => $post_id];
$persist = function () use (&$manifest, $manifest_path): void {
    file_put_contents($manifest_path, wp_json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
};
$persist();
$image_posts = get_posts(['posts_per_page' => 1, 'meta_key' => '_thumbnail_id', 'post__not_in' => [$post_id]]);
if ($image_posts) {
    set_post_thumbnail($post_id, get_post_thumbnail_id($image_posts[0]));
}
wp_set_current_user($admins[0]->ID);
$_POST = [
    'capao_guia_post_nonce' => wp_create_nonce('capao_guia_save_post'),
    'capao_guia_address' => 'Rua de Teste, 123 — Capão Redondo',
    'capao_guia_hours' => "Segunda a sábado: 11h às 22h\nDomingo: 12h às 18h",
    'capao_guia_phone' => '(11) 99999-0000',
    'capao_guia_whatsapp' => 'https://wa.me/5511999990000',
    'capao_guia_website' => '',
];
wp_update_post(['ID' => $post_id]);
capao_guia_test_assert(get_post_meta($post_id, 'capao_guia_address', true) !== '', 'Campos salvos com nonce e permissão');
capao_guia_test_assert(get_post_meta($post_id, 'capao_guia_website', true) === '', 'Campo opcional vazio não salvo');
$_POST = ['capao_guia_address' => 'Tentativa sem nonce'];
wp_update_post(['ID' => $post_id]);
capao_guia_test_assert(get_post_meta($post_id, 'capao_guia_address', true) !== 'Tentativa sem nonce', 'Campos protegidos contra gravação sem nonce');
wp_set_current_user(0);
$_POST = ['capao_guia_review_nonce' => wp_create_nonce('capao_guia_review_' . $post_id), 'capao_guia_rating' => '5'];
$comment_data = [
    'comment_post_ID' => $post_id, 'comment_author' => 'Visitante de teste',
    'comment_author_email' => 'guia-teste@example.test', 'comment_content' => 'Experiência temporária para validar o Guia ' . $token,
    'comment_author_IP' => '192.0.2.10', 'comment_author_url' => '', 'comment_type' => 'comment',
];
$comment_id = wp_new_comment($comment_data, true);
if (is_wp_error($comment_id)) {
    throw new RuntimeException($comment_id->get_error_message());
}
capao_guia_test_assert(wp_get_comment_status($comment_id) === 'unapproved', 'Avaliação nova fica pendente');
capao_guia_test_assert((int) get_comment_meta($comment_id, 'capao_guia_rating', true) === 5, 'Nota salva no comentário nativo');
capao_guia_test_assert(capao_guia_rating_summary($post_id)['count'] === 0, 'Pendente fica fora da média');
$_POST = [];
wp_set_comment_status($comment_id, 'approve');
capao_guia_test_assert(capao_guia_rating_summary($post_id) === ['count' => 1, 'average' => 5.0], 'Aprovação atualiza média');
wp_set_current_user($admins[0]->ID);
$_POST = ['capao_guia_rating_nonce' => wp_create_nonce('capao_guia_edit_rating_' . $comment_id), 'capao_guia_admin_rating' => '4'];
wp_update_comment(['comment_ID' => $comment_id, 'comment_content' => $comment_data['comment_content']]);
capao_guia_test_assert(capao_guia_rating_summary($post_id) === ['count' => 1, 'average' => 4.0], 'Nota editada no fluxo de edição atualiza média');
wp_set_current_user(0);
$_POST = ['capao_guia_rating_nonce' => wp_create_nonce('capao_guia_edit_rating_' . $comment_id), 'capao_guia_admin_rating' => '1'];
do_action('edit_comment', $comment_id);
capao_guia_test_assert((int) get_comment_meta($comment_id, 'capao_guia_rating', true) === 4, 'Visitante não pode editar nota');
$_POST = [];
wp_set_comment_status($comment_id, 'spam');
capao_guia_test_assert(capao_guia_rating_summary($post_id)['count'] === 0, 'Spam sai da média');
wp_set_comment_status($comment_id, 'approve');
wp_trash_comment($comment_id);
capao_guia_test_assert(capao_guia_rating_summary($post_id)['count'] === 0, 'Lixeira sai da média');
wp_untrash_comment($comment_id);
wp_set_comment_status($comment_id, 'approve');
wp_set_comment_status($comment_id, 'hold');
capao_guia_test_assert(capao_guia_rating_summary($post_id)['count'] === 0, 'Reprovação sai da média');
wp_set_comment_status($comment_id, 'approve');
$second = wp_insert_comment(array_merge($comment_data, [
    'comment_author' => 'Segunda visita', 'comment_content' => 'Comentário aprovado de teste.',
    'comment_approved' => 1, 'comment_meta' => ['capao_guia_rating' => 2],
]));
wp_insert_comment(array_merge($comment_data, ['comment_content' => 'Comentário antigo, sem nota.', 'comment_approved' => 1]));
capao_guia_test_assert(capao_guia_rating_summary($post_id) === ['count' => 2, 'average' => 3.0], 'Média pondera notas e exclui comentário antigo sem nota');
$invalid_stored = wp_insert_comment(array_merge($comment_data, ['comment_content' => 'Nota legada inválida de teste.', 'comment_approved' => 1, 'comment_meta' => ['capao_guia_rating' => 9]]));
capao_guia_test_assert(capao_guia_rating_summary($post_id)['count'] === 2, 'Nota antiga inválida fica fora da média');
wp_delete_comment($second, true);
capao_guia_test_assert(capao_guia_rating_summary($post_id) === ['count' => 1, 'average' => 4.0], 'Exclusão definitiva atualiza média');
add_filter('wp_die_handler', function () {
    return function ($message) { throw new RuntimeException(wp_strip_all_tags((string) $message)); };
});
foreach ([null, '0', '6', '3.5', '5abc', ['5'], "5\n"] as $bad) {
    $_POST = ['capao_guia_review_nonce' => wp_create_nonce('capao_guia_review_' . $post_id)];
    if ($bad !== null) {
        $_POST['capao_guia_rating'] = $bad;
    }
    $rejected = false;
    try { apply_filters('preprocess_comment', $comment_data); } catch (RuntimeException $e) { $rejected = true; }
    capao_guia_test_assert($rejected, 'Nota ausente ou inválida rejeitada: ' . wp_json_encode($bad));
}
$_POST = ['capao_guia_rating' => '5'];
$rejected = false;
try { apply_filters('preprocess_comment', $comment_data); } catch (RuntimeException $e) { $rejected = true; }
capao_guia_test_assert($rejected, 'Avaliação sem nonce rejeitada');
$_POST = ['capao_guia_review_nonce' => wp_create_nonce('capao_guia_review_' . $post_id), 'capao_guia_rating' => '5'];
$duplicate = wp_new_comment($comment_data, true);
capao_guia_test_assert(is_wp_error($duplicate) && $duplicate->get_error_code() === 'comment_duplicate', 'Proteção nativa contra comentário duplicado preservada');
$_POST = [];
$ordinary = wp_insert_post(['post_title' => '[TESTE TEMPORÁRIO] Comentários comuns', 'post_status' => 'draft', 'post_type' => 'post', 'comment_status' => 'open', 'meta_input' => ['_capao_guia_test_fixture' => $token]]);
$manifest['post_ids'][] = $ordinary;
$persist();
$ordinary_data = array_merge($comment_data, ['comment_post_ID' => $ordinary]);
capao_guia_test_assert(apply_filters('preprocess_comment', $ordinary_data) === $ordinary_data, 'Comentário comum não exige nota ou nonce do guia');
capao_guia_test_assert(apply_filters('pre_comment_approved', 1, $ordinary_data) === 1, 'Moderação comum preservada');
capao_guia_test_assert(apply_filters('pre_comment_approved', 'spam', $comment_data) === 'spam', 'Classificação antispam não é sobrescrita');
// Enough explicitly temporary guide posts to exercise native pagination.
$page_size = max(1, (int) get_option('posts_per_page'));
for ($i = 0; $i < $page_size; $i++) {
    $id = wp_insert_post(['post_title' => '[TESTE TEMPORÁRIO] Lugar do guia ' . ($i + 1), 'post_content' => 'Matéria temporária para conferir a paginação.', 'post_status' => 'publish', 'post_type' => 'post', 'post_category' => [$restaurant->term_id], 'meta_input' => ['_capao_guia_test_fixture' => $token]]);
    $manifest['post_ids'][] = $id;
    $persist();
}
$manifest['post_url'] = get_permalink($post_id);
$manifest['category_url'] = get_category_link($root_id);
$manifest['subcategory_url'] = get_category_link($restaurant->term_id);
$manifest['checks'] = $checks;
$persist();
echo wp_json_encode(['checks' => $checks, 'fixture' => $manifest_path, 'post_url' => $manifest['post_url'], 'category_url' => $manifest['category_url']], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
