<?php
/**
 * The template for displaying WordPress pages.
 *
 * @package Capao_News
 */
get_header();
?>

<?php while (have_posts()) : the_post(); ?>
    <?php if (is_page('nossa-equipe')) : ?>
        <?php
        $team_users = get_users([
            'orderby' => 'display_name',
            'order' => 'ASC',
            'role__in' => ['administrator', 'editor', 'author'],
        ]);
        ?>
        <article id="page-<?php the_ID(); ?>" class="team-page">
            <header class="team-page-header">
                <h1><?php the_title(); ?></h1>
            </header>

            <?php if (! empty($team_users)) : ?>
                <div class="team-grid">
                    <?php foreach ($team_users as $team_user) : ?>
                        <?php
                        $user_roles = $team_user->roles ?? [];
                        $role_key = ! empty($user_roles) ? $user_roles[0] : 'author';
                        $wp_roles = wp_roles();
                        $role_label = isset($wp_roles->role_names[$role_key]) ? $wp_roles->role_names[$role_key] : 'Colaborador';
                        $bio = trim((string) get_user_meta($team_user->ID, 'description', true));
                        ?>
                        <div class="team-member">
                            <?php echo get_avatar($team_user->ID, 140, '', $team_user->display_name, ['class' => 'team-member-image']); ?>
                            <h2><?php echo esc_html($team_user->display_name); ?></h2>
                            <p><?php echo esc_html($bio ?: $role_label); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
    <?php else : ?>
        <article id="page-<?php the_ID(); ?>" <?php post_class('mx-auto max-w-4xl'); ?>>
            <header class="border-b border-[#202020]/20 pb-5">
                <p class="text-[10px] font-bold uppercase tracking-[.2em] text-[#4a154b]">
                    <?php esc_html_e('Capão News', 'capao-news'); ?>
                </p>
                <h1 class="mt-3 font-serif text-4xl font-black leading-tight sm:text-6xl"><?php the_title(); ?></h1>
            </header>

            <div class="prose prose-lg mt-8 max-w-none font-serif leading-relaxed">
                <?php if (is_page('quem-somos')) : ?>
                    <?php echo wp_kses_post(capao_news_about_content()); ?>
                <?php else : ?>
                    <?php the_content(); ?>
                <?php endif; ?>
            </div>
        </article>
    <?php endif; ?>
<?php endwhile; ?>

<?php get_footer(); ?>
