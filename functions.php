<?php
/**
 * Capão News theme functions.
 */

declare(strict_types=1);

function capao_news_setup(): void
{
    load_theme_textdomain('capao-news', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo', [
        'height'      => 150,
        'width'       => 1440,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('custom-header', [
        'width'       => 1440,
        'height'      => 150,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    add_image_size('capao-ad-banner', 980, 140, true);
    add_image_size('capao-ad-sidebar', 300, 250, true);

    register_nav_menus([
        'primary' => __('Navegação principal', 'capao-news'),
        'footer'  => __('Navegação do rodapé', 'capao-news'),
    ]);
}
add_action('after_setup_theme', 'capao_news_setup');

function capao_news_enqueue_assets(): void
{
    $theme_version = wp_get_theme()->get('Version');
    $style_path = get_template_directory() . '/style.css';
    $tailwind_path = get_template_directory() . '/assets/css/tailwind.css';

    wp_enqueue_style(
        'capao-news-google-fonts',
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'capao-news-style',
        get_stylesheet_uri(),
        [],
        file_exists($style_path) ? (string) filemtime($style_path) : $theme_version
    );

    if (file_exists($tailwind_path)) {
        wp_enqueue_style(
            'capao-news-tailwind',
            get_template_directory_uri() . '/assets/css/tailwind.css',
            ['capao-news-style'],
            (string) filemtime($tailwind_path)
        );
    }

    $script_path = get_template_directory() . '/assets/js/theme.js';
    if (file_exists($script_path)) {
        wp_enqueue_script('capao-news-theme', get_template_directory_uri() . '/assets/js/theme.js', [], (string) filemtime($script_path), true);
    }
}
add_action('wp_enqueue_scripts', 'capao_news_enqueue_assets');

function capao_news_register_content_types(): void
{
    register_post_type('anuncio', [
        'labels' => [
            'name' => __('Anúncios', 'capao-news'),
            'singular_name' => __('Anúncio', 'capao-news'),
            'add_new_item' => __('Adicionar novo anúncio', 'capao-news'),
            'edit_item' => __('Editar anúncio', 'capao-news'),
        ],
        'public' => true,
        'show_ui' => true,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => ['title', 'thumbnail', 'editor'],
        'has_archive' => false,
        'rewrite' => ['slug' => 'anuncios'],
        'show_in_rest' => true,
    ]);

    register_post_type('parceiro', [
        'labels' => [
            'name' => __('Parceiros', 'capao-news'),
            'singular_name' => __('Parceiro', 'capao-news'),
            'add_new_item' => __('Adicionar novo parceiro', 'capao-news'),
            'edit_item' => __('Editar parceiro', 'capao-news'),
        ],
        'public' => true,
        'show_ui' => true,
        'menu_icon' => 'dashicons-groups',
        'supports' => ['title', 'thumbnail', 'editor'],
        'has_archive' => false,
        'rewrite' => ['slug' => 'parceiros'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'capao_news_register_content_types');

function capao_news_register_promo_meta_boxes(): void
{
    add_meta_box('capao_news_promo_details', __('Detalhes do item', 'capao-news'), 'capao_news_render_promo_meta_box', ['anuncio', 'parceiro'], 'normal', 'default');
}
add_action('add_meta_boxes', 'capao_news_register_promo_meta_boxes');

function capao_news_render_promo_meta_box($post): void
{
    wp_nonce_field('capao_news_save_promo_fields', 'capao_news_promo_nonce');

    $link = get_post_meta($post->ID, 'capao_news_link', true);
    $status = get_post_meta($post->ID, 'capao_news_status', true);
    ?>
    <p>
        <label for="capao_news_link"><strong><?php esc_html_e('Link do item', 'capao-news'); ?></strong></label><br>
        <input type="url" id="capao_news_link" name="capao_news_link" value="<?php echo esc_attr($link ?: ''); ?>" class="widefat" placeholder="https://exemplo.com">
    </p>
    <p>
        <label for="capao_news_status">
            <input type="checkbox" id="capao_news_status" name="capao_news_status" value="1" <?php checked((string) $status, '1'); ?>>
            <?php esc_html_e('Ativar este item no site', 'capao-news'); ?>
        </label>
    </p>
    <?php
}

function capao_news_save_promo_meta(int $post_id): void
{
    if (!isset($_POST['capao_news_promo_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['capao_news_promo_nonce'])), 'capao_news_save_promo_fields')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $link = isset($_POST['capao_news_link']) ? esc_url_raw(wp_unslash($_POST['capao_news_link'])) : '';
    $status = isset($_POST['capao_news_status']) ? '1' : '0';

    update_post_meta($post_id, 'capao_news_link', $link);
    update_post_meta($post_id, 'capao_news_status', $status);
}
add_action('save_post', 'capao_news_save_promo_meta');

function capao_news_reading_time(): string
{
    $content = get_the_content();
    $word_count = str_word_count(wp_strip_all_tags($content));
    $minutes = max(1, (int) ceil($word_count / 180));

    return sprintf('%d min de leitura', $minutes);
}

function capao_news_guide_category_id(): int
{
    $stored = (int) get_option('capao_guia_category_id');
    if ($stored && get_term($stored, 'category') instanceof WP_Term) {
        return $stored;
    }
    $category = get_term_by('slug', 'guia-capao', 'category');
    return $category ? (int) $category->term_id : 0;
}

function capao_news_is_guide_category($category): bool
{
    if (!($category instanceof WP_Term)) {
        return false;
    }
    $root = capao_news_guide_category_id();
    return $root && ((int) $category->term_id === $root || in_array($root, array_map('intval', get_ancestors($category->term_id, 'category')), true));
}

function capao_news_guide_rating_fields(int $post_id): void
{
    ?>
    <fieldset class="guia-rating-input">
        <legend>Sua nota <span aria-hidden="true">*</span></legend>
        <div class="guia-rating-options">
            <?php for ($i = 1; $i <= 5; $i++) : ?>
                <label><input type="radio" name="capao_guia_rating" value="<?php echo esc_attr((string) $i); ?>" required>
                    <span aria-hidden="true"><?php echo esc_html(str_repeat('★', $i)); ?></span>
                    <span class="guia-sr-only"><?php echo esc_html($i . ($i === 1 ? ' estrela' : ' estrelas')); ?></span>
                </label>
            <?php endfor; ?>
        </div>
    </fieldset>
    <?php
}
add_action('capao_guia_rating_form', 'capao_news_guide_rating_fields');

function capao_news_guide_comment($comment, array $args, int $depth): void
{
    if ((string) $comment->comment_approved !== '1') {
        return;
    }
    $rating = get_comment_meta($comment->comment_ID, 'capao_guia_rating', true);
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class('guia-comment', $comment); ?>>
        <article>
            <header><strong><?php echo esc_html(get_comment_author($comment)); ?></strong>
                <time datetime="<?php echo esc_attr(get_comment_date('c', $comment)); ?>"><?php echo esc_html(get_comment_date(get_option('date_format'), $comment)); ?></time>
            </header>
            <?php if (function_exists('capao_guia_valid_rating') && capao_guia_valid_rating($rating)) : ?>
                <p class="guia-comment-stars" aria-label="<?php echo esc_attr($rating . ' de 5 estrelas'); ?>"><span aria-hidden="true"><?php echo esc_html(str_repeat('★', (int) $rating) . str_repeat('☆', 5 - (int) $rating)); ?></span></p>
            <?php endif; ?>
            <div class="guia-comment-text"><?php comment_text($comment); ?></div>
        </article>
    <?php
    // wp_list_comments closes the list item through its native walker.
}

function capao_news_menu_fallback(): void
{
    $categories = [
        'Início'             => home_url('/'),
        'Da Ponte pra Cá'    => 'da-ponte-pra-ca',
        'Feito no Capão'     => 'feito-no-capao',
        'Bora Lá?'           => 'bora-la',
        'Capão que eu quero' => 'capao-que-eu-quero',
        'Gente Nossa'        => 'gente-nossa',
        'Guia Capão'         => capao_news_guide_category_id() ? get_category_link(capao_news_guide_category_id()) : home_url('/category/guia-capao/'),
    ];

    echo '<ul class="site-menu flex flex-wrap items-center justify-center gap-2 lg:gap-3">';
    foreach ($categories as $label => $target) {
        $category = is_string($target) ? get_category_by_slug($target) : null;
        $url = $category ? get_category_link($category->term_id) : $target;
        $current = ($category && is_category($category->term_id))
            || ($target === home_url('/') && is_front_page())
            || ($label === 'Guia Capão' && is_category() && capao_news_is_guide_category(get_queried_object()));

        printf(
            '<li class="%s"><a class="whitespace-nowrap rounded-full px-4 py-2 transition hover:bg-[#6865a8]/10 hover:text-[#4a154b]" href="%s"%s>%s</a></li>',
            $current ? 'current-menu-item' : '',
            esc_url($url),
            $current ? ' aria-current="page"' : '',
            esc_html($label)
        );
    }
    echo '</ul>';
}

function capao_news_create_static_pages(): void
{
    $quem_somos_content = capao_news_about_content();

    $pages = [
        'quem-somos' => [
            'title'   => 'Quem Somos',
            'content' => $quem_somos_content,
        ],
        'nossa-equipe' => [
            'title'   => 'Nossa Equipe',
            'content' => '<p>O Capão News é feito por uma equipe apaixonada por jornalismo local, por histórias de bairro e por fortalecer a comunicação da comunidade.</p><p>Trabalhamos com produção editorial, apuração, redação, cobertura de eventos, entrevistas e acompanhamento do que acontece na região. Nossa rotina mistura presença no território, escuta com a comunidade e cuidado para transformar fatos em informação útil, honesta e acessível.</p><p>Acreditamos que a região precisa de veículos que olhem de perto para as reais necessidades da população, valorizando quem vive, produz e constrói o Capão todos os dias.</p>',
        ],
    ];

    foreach ($pages as $slug => $page_data) {
        $existing_page = get_page_by_path($slug, OBJECT, 'page');

        if ($existing_page) {
            if ($slug === 'quem-somos' && strpos((string) $existing_page->post_content, 'O Capão News nasceu para dar voz ao bairro') !== false) {
                wp_update_post([
                    'ID'           => $existing_page->ID,
                    'post_content' => $page_data['content'],
                ]);
            }

            continue;
        }

        wp_insert_post([
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => $page_data['title'],
            'post_name'    => $slug,
            'post_content' => $page_data['content'],
            'post_author'  => get_current_user_id() ?: 1,
        ]);
    }
}
add_action('after_switch_theme', 'capao_news_create_static_pages');
add_action('init', 'capao_news_create_static_pages');

function capao_news_about_content(): string
{
    return '<h2><strong>Jornalismo feito daqui, para quem vive aqui.</strong></h2><p>O Capão News é um veículo de comunicação independente, criado pelo jornalista Nailson Costa para informar quem vive, trabalha e constrói todos os dias o Capão Redondo e a Zona Sul de São Paulo.</p><p>Produzimos jornalismo da ponte pra cá com responsabilidade, linguagem acessível e compromisso com quem vive o território.</p><p>Noticiamos os problemas, cobramos soluções, fiscalizamos o poder público e também mostramos as histórias, os negócios, a cultura e as pessoas que fazem a quebrada acontecer.</p><p>Com mais de 200 mil seguidores nas redes sociais e milhões de visualizações todos os meses, trabalhamos para fortalecer uma comunidade mais informada, crítica e conectada com o próprio lugar onde mora.</p><p>Mas números são apenas parte da nossa história.</p><p>Nossa principal força está na confiança construída com quem está do lado de cá da ponte.</p><p><strong>Capão News. Notícias da Ponte pra Cá.</strong></p>';
}
