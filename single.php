<?php
/**
 * Template for individual news posts.
 *
 * @package Capao_News
 */
get_header();
?>

<?php while (have_posts()) : the_post(); ?>
    <article class="single-story">
        <header class="single-header">
            <div class="single-kicker">
                <span><?php echo esc_html(get_the_category()[0]->name ?? 'Capão News'); ?></span>
                <span><?php echo esc_html(get_the_date('j \d\e F \d\e Y')); ?></span>
            </div>
            <h1><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?>
                <p class="single-deck"><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php endif; ?>
            <div class="single-byline">
                <span><?php esc_html_e('Publicado por', 'capao-news'); ?> <?php the_author(); ?></span>
                <span><?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp'))); ?> <?php esc_html_e('atrás', 'capao-news'); ?></span>
                <span class="single-byline__reading-time"><?php echo esc_html(capao_news_reading_time()); ?></span>
            </div>
        </header>

        <div class="reading-tools" aria-label="<?php esc_attr_e('Ferramentas de acessibilidade', 'capao-news'); ?>">
            <span class="reading-tools-label"><?php esc_html_e('Acessibilidade', 'capao-news'); ?></span>
            <button type="button" data-read-article><?php esc_html_e('Ouvir notícia', 'capao-news'); ?></button>
            <button type="button" data-stop-reading disabled><?php esc_html_e('Parar leitura', 'capao-news'); ?></button>
            <button type="button" data-contrast-toggle aria-pressed="false"><?php esc_html_e('Alto contraste', 'capao-news'); ?></button>
            <span class="reading-status" data-reading-status aria-live="polite"></span>
        </div>

        <?php if (has_post_thumbnail()) : ?>
            <figure class="single-hero-image">
                <?php the_post_thumbnail('full'); ?>
                <?php if (get_the_post_thumbnail_caption()) : ?><figcaption><?php echo esc_html(get_the_post_thumbnail_caption()); ?></figcaption><?php endif; ?>
            </figure>
        <?php endif; ?>

        <div class="single-layout">
            <aside class="single-share" aria-label="<?php esc_attr_e('Compartilhar matéria', 'capao-news'); ?>">
                <span><?php esc_html_e('Compartilhe', 'capao-news'); ?></span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode(get_permalink()); ?>" target="_blank" rel="noopener" aria-label="Facebook">f</a>
                <a href="https://wa.me/?text=<?php echo rawurlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" rel="noopener" aria-label="WhatsApp">w</a>
                <button type="button" data-copy-link aria-label="<?php esc_attr_e('Copiar link', 'capao-news'); ?>">↗</button>
            </aside>
            <div class="single-content">
                <?php the_content(); ?>
                <?php get_template_part('template-parts/guia-practical'); ?>
                <div class="single-tags">
                    <?php the_tags('<span>' . esc_html__('Tags', 'capao-news') . '</span>', ' ', ''); ?>
                </div>
            </div>
        </div>
    </article>

    <?php if (function_exists('capao_guia_is_post') && capao_guia_is_post((int) get_the_ID())) : ?>
        <?php comments_template('/comments-guia.php'); ?>
    <?php endif; ?>

    <?php
    $related_posts = new WP_Query([
        'posts_per_page' => 3,
        'post__not_in'   => [get_the_ID()],
        'category__in'   => wp_get_post_categories(get_the_ID()),
        'post_status'    => 'publish',
    ]);
    ?>
    <?php if ($related_posts->have_posts()) : ?>
        <section class="related-section" aria-label="<?php esc_attr_e('Leia também', 'capao-news'); ?>">
            <div class="section-heading"><h2><?php esc_html_e('Leia também', 'capao-news'); ?></h2></div>
            <div class="latest-grid">
                <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                    <article class="latest-story">
                        <?php if (has_post_thumbnail()) : ?><a class="latest-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium_large'); ?></a><?php endif; ?>
                        <p class="eyebrow"><?php echo esc_html(get_the_category()[0]->name ?? 'Capão News'); ?></p>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
<?php endwhile; ?>

<?php get_footer(); ?>
