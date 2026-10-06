<?php
/**
 * The header for the Capão News theme.
 *
 * @package Capao_News
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('capao-site'); ?>>
<?php wp_body_open(); ?>
<a class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-[#6865a8] focus:px-4 focus:py-3 focus:text-sm focus:font-bold focus:text-white" href="#conteudo">
    <?php esc_html_e('Pular para o conteúdo', 'capao-news'); ?>
</a>

<header class="site-header">
    <div class="header-inner">
        <div class="header-branding">
        <?php if (has_custom_logo()) : ?>
            <div class="header-logo-wrap">
                <?php the_custom_logo(); ?>
            </div>
        <?php elseif (has_custom_header() && get_header_image()) : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Capão News - início', 'capao-news'); ?>">
                <img class="header-banner-image" src="<?php echo esc_url(get_header_image()); ?>" alt="<?php bloginfo('name'); ?>">
            </a>
        <?php else : ?>
            <a class="brand-link" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Capão News - início', 'capao-news'); ?>">
                <span class="brand-wordmark" aria-label="Capão News">
                    <span class="brand-capao">CAPÃO</span>
                    <span class="brand-news">news</span>
                </span>
                <span class="brand-tagline"><?php esc_html_e('Notícia feita na quebrada', 'capao-news'); ?></span>
            </a>
        <?php endif; ?>
        </div>
        <button class="mobile-menu-toggle" type="button" aria-expanded="false" aria-controls="site-menu-drawer" data-open-label="<?php esc_attr_e('Abrir menu', 'capao-news'); ?>" data-close-label="<?php esc_attr_e('Fechar menu', 'capao-news'); ?>">
            <span class="screen-reader-text"><?php esc_html_e('Abrir menu', 'capao-news'); ?></span><span class="mobile-menu-icon" aria-hidden="true"><i></i><i></i><i></i></span>
        </button>
    </div>

    <button class="menu-backdrop" type="button" tabindex="-1" aria-label="<?php esc_attr_e('Fechar menu', 'capao-news'); ?>" hidden></button>
    <div id="site-menu-drawer" class="menu-drawer" data-expand-label="<?php esc_attr_e('Expandir submenu', 'capao-news'); ?>" data-collapse-label="<?php esc_attr_e('Recolher submenu', 'capao-news'); ?>">
        <div class="menu-drawer-surface">
            <div class="menu-drawer-heading">
                <h2 id="mobile-menu-title"><?php esc_html_e('Menu', 'capao-news'); ?></h2>
                <button class="mobile-menu-close" type="button" aria-label="<?php esc_attr_e('Fechar menu', 'capao-news'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
    <nav id="site-menu-panel" class="site-navigation" aria-label="<?php esc_attr_e('Navegação principal', 'capao-news'); ?>">
        <div class="site-navigation-inner">
            <form class="site-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="site-search-input"><?php esc_html_e('Buscar notícias', 'capao-news'); ?></label>
                <input id="site-search-input" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php esc_attr_e('Buscar', 'capao-news'); ?>">
                <button type="submit" aria-label="<?php esc_attr_e('Buscar notícias', 'capao-news'); ?>">⌕</button>
            </form>
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'fallback_cb'    => 'capao_news_menu_fallback',
                'menu_class'     => 'site-menu',
                'depth'          => 2,
            ]);
            ?>

        </div>
    </nav>
        </div>
    </div>
</header>

<div class="desktop-compact-header" hidden inert>
    <div class="desktop-compact-inner">
        <div class="desktop-compact-brand"></div>
        <nav class="desktop-compact-editorias" aria-label="<?php esc_attr_e('Editorias', 'capao-news'); ?>"></nav>
        <details class="desktop-compact-menu">
            <summary><?php esc_html_e('Menu completo', 'capao-news'); ?></summary>
            <nav aria-label="<?php esc_attr_e('Menu completo', 'capao-news'); ?>"></nav>
        </details>
        <div class="desktop-compact-search"></div>
    </div>
</div>

<main id="conteudo" class="site-main">
