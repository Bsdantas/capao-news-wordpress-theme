<?php
/** Category archives, with a dedicated presentation for Guia Capão. */
$term = get_queried_object();
if (!($term instanceof WP_Term) || !capao_news_is_guide_category($term)) {
    get_template_part('index');
    return;
}
$root_id = capao_news_guide_category_id();
$root = get_term($root_id, 'category');
$children = get_terms(['taxonomy' => 'category', 'parent' => $root_id, 'hide_empty' => false]);
get_header();
?>
<section class="guia-archive">
    <header class="guia-archive-header">
        <p class="eyebrow"><?php esc_html_e('Conheça o território', 'capao-news'); ?></p>
        <h1><?php echo esc_html($term->term_id === $root_id ? 'Guia Capão' : $term->name); ?></h1>
        <p>Sabores, encontros e histórias do Capão Redondo e da Zona Sul. Explore os lugares da região e compartilhe sua experiência.</p>
        <?php if ($term->description) : ?><div class="guia-category-description"><?php echo wp_kses_post(wpautop($term->description)); ?></div><?php endif; ?>
    </header>
    <nav class="guia-category-nav" aria-label="<?php esc_attr_e('Categorias do Guia Capão', 'capao-news'); ?>">
        <?php if ($root && !is_wp_error($root)) : ?>
            <a href="<?php echo esc_url(get_term_link($root)); ?>" <?php if ($term->term_id === $root_id) : ?>aria-current="page"<?php endif; ?>>Todos os lugares</a>
        <?php endif; ?>
        <?php if (!is_wp_error($children)) : foreach ($children as $child) : ?>
            <a href="<?php echo esc_url(get_term_link($child)); ?>" <?php if ($term->term_id === $child->term_id) : ?>aria-current="page"<?php endif; ?>><?php echo esc_html($child->name); ?></a>
        <?php endforeach; endif; ?>
    </nav>
    <?php if (have_posts()) : ?>
        <div class="guia-cards">
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('guia-card'); ?>>
                    <?php if (has_post_thumbnail()) : ?><a class="guia-card-image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail('medium_large'); ?></a><?php endif; ?>
                    <div class="guia-card-body">
                        <p class="eyebrow"><?php echo esc_html(implode(' · ', array_map(function ($category) { return $category->name; }, array_filter(get_the_category(), 'capao_news_is_guide_category')))); ?></p>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
                        <?php if (function_exists('capao_guia_rating_summary')) : $rating = capao_guia_rating_summary((int) get_the_ID()); ?>
                            <p class="guia-card-rating"><?php echo $rating['count'] ? esc_html('★ ' . number_format_i18n($rating['average'], 1) . ' / 5 · ' . $rating['count'] . ' avaliações') : 'Ainda não há avaliações'; ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        <div class="guia-pagination"><?php the_posts_pagination(['mid_size' => 1, 'prev_text' => '← Anterior', 'next_text' => 'Próxima →']); ?></div>
    <?php else : ?>
        <p class="guia-empty">Em breve, novas matérias e lugares para descobrir nesta categoria.</p>
    <?php endif; ?>
</section>
<?php get_footer(); ?>
