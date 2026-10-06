<?php
/** Native comments presentation exclusively for guide posts. */
if (!function_exists('capao_guia_is_post') || !capao_guia_is_post((int) get_the_ID()) || post_password_required()) {
    return;
}
$summary = capao_guia_rating_summary((int) get_the_ID());
$commenter = wp_get_current_commenter();
$form_fields = [
    'author' => '<p class="comment-form-author"><label for="author">Nome *</label><input id="author" name="author" type="text" autocomplete="name" maxlength="245" value="' . esc_attr($commenter['comment_author']) . '" required></p>',
    'email' => '<p class="comment-form-email"><label for="email">E-mail *</label><input id="email" name="email" type="email" autocomplete="email" maxlength="100" value="' . esc_attr($commenter['comment_author_email']) . '" required></p>',
];
if (get_option('show_comments_cookies_opt_in')) {
    $form_fields['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . checked(!empty($commenter['comment_author_email']), true, false) . '><label for="wp-comment-cookies-consent">Salvar meu nome e e-mail neste navegador para a próxima avaliação.</label></p>';
}
?>
<section id="comments" class="guia-reviews" aria-labelledby="guia-reviews-title">
    <header class="guia-reviews-header">
        <h2 id="guia-reviews-title">Experiências da comunidade</h2>
        <p class="guia-rating-summary">
            <?php if ($summary['count']) : ?>
                <span aria-hidden="true">★</span> <strong><?php echo esc_html(number_format_i18n($summary['average'], 1)); ?> / 5</strong>
                <span><?php echo esc_html(sprintf(_n('%s avaliação aprovada', '%s avaliações aprovadas', $summary['count'], 'capao-news'), number_format_i18n($summary['count']))); ?></span>
            <?php else : ?>Ainda não há avaliações<?php endif; ?>
        </p>
    </header>
    <?php if (have_comments()) : ?>
        <ol class="guia-comment-list"><?php wp_list_comments(['style' => 'ol', 'type' => 'comment', 'callback' => 'capao_news_guide_comment', 'max_depth' => 1]); ?></ol>
        <nav class="guia-pagination" aria-label="Páginas de avaliações"><?php paginate_comments_links(); ?></nav>
    <?php endif; ?>
    <?php if (comments_open()) : ?>
        <p class="guia-review-notice">Sua avaliação será publicada após aprovação. O e-mail não será exibido publicamente.</p>
        <?php comment_form([
            'title_reply' => 'Conte sua experiência', 'label_submit' => 'Enviar avaliação',
            'comment_notes_before' => '<p>Informe os campos obrigatórios e selecione uma nota de 1 a 5 estrelas.</p>',
            'fields' => $form_fields,
            'comment_field' => '<p class="comment-form-comment"><label for="comment">Seu comentário *</label><textarea id="comment" name="comment" rows="5" maxlength="65525" required></textarea></p>',
        ]); ?>
    <?php else : ?><p>As avaliações estão fechadas para esta matéria.</p><?php endif; ?>
</section>
