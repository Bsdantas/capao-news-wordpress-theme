<?php
/**
 * The main template file.
 *
 * @package Capao_News
 */
get_header();
?>

<?php if (is_category()) : get_template_part('template-parts/editorial-header'); endif; ?>

<?php if (is_home() || is_front_page()) : ?>
    <?php
    $promo_ad = new WP_Query([
        'post_type' => 'anuncio',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => [
            ['key' => 'capao_news_status', 'value' => '1', 'compare' => '='],
        ],
    ]);
    $top_ad_ids = [];
    ?>
    <?php if ($promo_ad->have_posts()) : ?>
        <?php while ($promo_ad->have_posts()) : $promo_ad->the_post(); $promo_link = get_post_meta(get_the_ID(), 'capao_news_link', true); $top_ad_ids[] = get_the_ID(); ?>
            <div class="ad-slot ad-slot--top" aria-label="<?php esc_attr_e('Espaço publicitário', 'capao-news'); ?>">
                <a href="<?php echo esc_url($promo_link ?: '#'); ?>" target="_blank" rel="noopener noreferrer">
                    <?php if (has_post_thumbnail()) : the_post_thumbnail('capao-ad-banner'); else : ?>
                        <?php echo esc_html(get_the_title()); ?>
                    <?php endif; ?>
                </a>
            </div>
        <?php endwhile; ?>
    <?php else : ?>
        <div class="ad-slot ad-slot--top" aria-label="<?php esc_attr_e('Espaço publicitário', 'capao-news'); ?>"><?php esc_html_e('Espaço para apoiador ou publicidade', 'capao-news'); ?></div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

    <?php
    $featured_posts = new WP_Query(['posts_per_page' => 5, 'post_status' => 'publish']);
    $popular_posts = new WP_Query(['posts_per_page' => 4, 'post_status' => 'publish', 'orderby' => 'comment_count']);
    ?>
    <?php if ($featured_posts->have_posts()) : ?>
        <section class="home-lead" aria-label="<?php esc_attr_e('Destaques do Capão News', 'capao-news'); ?>">
            <div class="home-news-column">
                <div class="featured-carousel" data-featured-carousel>
                    <div class="featured-slides">
                        <?php while ($featured_posts->have_posts()) : $featured_posts->the_post(); ?>
                            <article class="featured-slide<?php echo esc_attr($featured_posts->current_post === 0 ? ' is-active' : ''); ?>">
                                <?php if (has_post_thumbnail()) : ?><a class="lead-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a><?php endif; ?>
                                <p class="eyebrow"><?php echo esc_html(get_the_category()[0]->name ?? 'Capão News'); ?></p>
                                <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                                <p class="lead-summary"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></p>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <div class="featured-carousel-controls">
                        <button type="button" class="carousel-arrow" data-carousel-prev aria-label="Notícia anterior">←</button>
                        <div class="featured-carousel-dots" aria-label="Navegação de destaques">
                            <?php $dot_index = 0; while ($featured_posts->have_posts()) : $featured_posts->the_post(); ?>
                                <button type="button" class="carousel-dot<?php echo esc_attr($dot_index === 0 ? ' is-active' : ''); ?>" data-carousel-dot="<?php echo esc_attr((string) $dot_index); ?>" aria-label="Ir para o destaque <?php echo esc_attr((string) ($dot_index + 1)); ?>"></button>
                                <?php $dot_index++; ?>
                            <?php endwhile; ?>
                        </div>
                        <button type="button" class="carousel-arrow" data-carousel-next aria-label="Próxima notícia">→</button>
                    </div>
                </div>
                <div class="secondary-stories">
                <div class="section-heading"><p class="eyebrow"><?php esc_html_e('Mais notícias', 'capao-news'); ?></p><span>01—05</span></div>
                <?php $secondary_posts = new WP_Query(['posts_per_page' => 5, 'post_status' => 'publish', 'post__not_in' => wp_list_pluck($featured_posts->posts, 'ID')]); ?>
                <?php if ($secondary_posts->have_posts()) : while ($secondary_posts->have_posts()) : $secondary_posts->the_post(); ?>
                    <article class="secondary-story">
                        <?php if (has_post_thumbnail()) : ?><a class="secondary-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium_large'); ?></a><?php endif; ?>
                        <div><p class="eyebrow"><?php echo esc_html(get_the_category()[0]->name ?? 'Capão News'); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p class="story-date"><span class="story-date__day"><?php echo esc_html(get_the_date('j \d\e F, Y')); ?></span><span class="story-date__separator">•</span><span class="story-date__reading-time"><?php echo esc_html(capao_news_reading_time()); ?></span></p></div>
                    </article>
                <?php endwhile; endif; ?>
                </div>
            </div>
            <aside class="popular-column">
                <div class="section-heading"><p class="eyebrow"><?php esc_html_e('Agora em alta', 'capao-news'); ?></p><span>MAIS LIDAS</span></div>
                <?php $popular_index = 0; while ($popular_posts->have_posts()) : $popular_posts->the_post(); $popular_index++; ?>
                    <article class="popular-story"><span class="popular-number">0<?php echo esc_html($popular_index); ?></span><div><p class="eyebrow"><?php echo esc_html(get_the_category()[0]->name ?? 'Capão News'); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2></div></article>
                <?php endwhile; ?>
                <?php
                $sidebar_promo = new WP_Query([
                    'post_type' => 'parceiro',
                    'post_status' => 'publish',
                    'posts_per_page' => 1,
                    'meta_query' => [
                        ['key' => 'capao_news_status', 'value' => '1', 'compare' => '='],
                    ],
                ]);
                ?>
                <?php if ($sidebar_promo->have_posts()) : ?>
                    <?php while ($sidebar_promo->have_posts()) : $sidebar_promo->the_post(); $promo_link = get_post_meta(get_the_ID(), 'capao_news_link', true); ?>
                        <div class="sidebar-ad is-filled">
                            <a href="<?php echo esc_url($promo_link ?: '#'); ?>" target="_blank" rel="noopener noreferrer" aria-label="Parceiro">
                                <?php if (has_post_thumbnail()) : the_post_thumbnail('medium_large'); else : ?>
                                    <strong><?php the_title(); ?></strong>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="sidebar-ad"><strong><?php esc_html_e('Seu negócio pode estar aqui', 'capao-news'); ?></strong></div>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </aside>
        </section>
        <?php wp_reset_postdata(); ?>

        <?php $latest_posts = new WP_Query(['posts_per_page' => 6, 'offset' => 5, 'post_status' => 'publish']); ?>
        <?php if ($latest_posts->have_posts()) : ?>
            <section class="latest-section"><div class="section-heading"><h2><?php esc_html_e('Da nossa redação', 'capao-news'); ?></h2><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Ver todas', 'capao-news'); ?> →</a></div><div class="latest-grid">
                <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?><article class="latest-story"><?php if (has_post_thumbnail()) : ?><a class="latest-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium_large'); ?></a><?php endif; ?><p class="eyebrow"><?php echo esc_html(get_the_category()[0]->name ?? 'Capão News'); ?></p><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p class="story-date"><span class="story-date__day"><?php echo esc_html(get_the_date('j \d\e F, Y')); ?></span><span class="story-date__separator">•</span><span class="story-date__reading-time"><?php echo esc_html(capao_news_reading_time()); ?></span></p></article><?php endwhile; ?>
            </div></section>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>
    <?php endif; ?>
<?php elseif (have_posts()) : ?>
    <section class="article-grid">
        <?php while (have_posts()) : the_post(); ?><article class="latest-story"><?php if (has_post_thumbnail()) : ?><a class="latest-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a><?php endif; ?><p class="eyebrow"><?php echo esc_html(get_the_category()[0]->name ?? 'Capão News'); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p class="lead-summary"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p></article><?php endwhile; ?>
    </section>
<?php else : ?>
    <section class="empty-state"><h1><?php esc_html_e('Nenhum artigo encontrado.', 'capao-news'); ?></h1></section>
<?php endif; ?>

<?php get_footer(); ?>
