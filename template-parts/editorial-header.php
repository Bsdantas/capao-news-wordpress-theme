<?php
/** Shared editorial introduction, using the theme typography. */
$intro = capao_news_editorial_intro(get_queried_object());
if (!$intro) {
    return;
}
?>
<header class="editorial-header">
    <h1><?php echo esc_html($intro['title']); ?></h1>
    <p><?php echo esc_html($intro['description']); ?></p>
</header>
