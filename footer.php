<?php
/**
 * The footer for the Capão News theme.
 *
 * @package Capao_News
 */
?>
</main>

<footer id="footer" class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand-block">
                <div class="footer-logo-wrap" aria-label="Capão News">
                    <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="Capão News - início">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/Footer_Site.png'); ?>" alt="Capão News" class="footer-brand-image" />
                    </a>
                </div>
                <p class="footer-slogan"><strong><?php esc_html_e('Notícias da Ponte pra Cá.', 'capao-news'); ?></strong></p>
                <p class="footer-tagline"><?php esc_html_e('Jornal independente, criado por Nailson Costa, para informar quem vive e transforma o Capão Redondo e a Zona Sul de São Paulo.', 'capao-news'); ?></p>
            </div>
                <div class="footer-column footer-column--journal">
                    <h2><?php esc_html_e('O jornal', 'capao-news'); ?></h2>
                    <a href="<?php echo esc_url(home_url('/quem-somos/')); ?>"><?php esc_html_e('Quem somos', 'capao-news'); ?></a>
                    <a href="<?php echo esc_url(home_url('/nossa-equipe/')); ?>"><?php esc_html_e('Nossa equipe', 'capao-news'); ?></a>
                    <a href="mailto:<?php echo esc_attr(get_option('admin_email')); ?>"><?php esc_html_e('Entre em contato', 'capao-news'); ?></a>
                </div>
                <div class="footer-column footer-column--social">
                    <h2><?php esc_html_e('Redes sociais', 'capao-news'); ?></h2>
                    <div class="footer-network-icons">
                        <a href="https://www.instagram.com/capaonews/" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" class="icon-fill"/></svg></a>
                        <a href="https://www.facebook.com/CapaoNewsOficial/" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3.3 0-5 1.9-5 5v3H6v4h3v4h4v-4h3l1-4h-4V9c0-.7.3-1 1-1Z"/></svg></a>
                        <a href="https://www.tiktok.com/@capaonews" target="_blank" rel="noopener" aria-label="TikTok" title="TikTok"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 4c.6 1.7 2.1 3 4 3.2v2.9c-1.7-.1-3.3-.8-4.6-2.1v6.8a5.4 5.4 0 1 1-5.4-5.4c.3 0 .7 0 1 .1v3a2.4 2.4 0 1 0 1.8 2.3V4h3.2Z"/></svg></a>
                        <a href="https://x.com/capaonews_" target="_blank" rel="noopener" aria-label="X / Twitter" title="X / Twitter"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4l14 16M19 4L5 20"/></svg></a>
                        <a href="https://www.youtube.com/channel/UCcpZpPGhihMCcyUBUGYZHEQ/about" target="_blank" rel="noopener" aria-label="YouTube" title="YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 7.5a2.5 2.5 0 0 0-1.8-1.8C17.6 5.3 12 5.3 12 5.3s-5.6 0-7.2.4A2.5 2.5 0 0 0 3 7.5 26 26 0 0 0 2.7 12c0 1.5.1 3.1.3 4.5a2.5 2.5 0 0 0 1.8 1.8c1.6.4 7.2.4 7.2.4s5.6 0 7.2-.4a2.5 2.5 0 0 0 1.8-1.8c.2-1.4.3-3 .3-4.5s-.1-3.1-.3-4.5Z"/><path d="m10 9 5 3-5 3V9Z"/></svg></a>
                    </div>
                </div>
            <div class="footer-column footer-cta" id="apoie">
                <h2><?php esc_html_e('Fale com a redação', 'capao-news'); ?></h2>
                <p><?php esc_html_e('Envie pautas, sugestões ou denúncias.', 'capao-news'); ?></p>
                <a href="https://wa.me/5511960361360?text=Ol%C3%A1%2C%20quero%20falar%20com%20a%20reda%C3%A7%C3%A3o%20do%20Cap%C3%A3o%20News." target="_blank" rel="noopener"><?php esc_html_e('Entrar em contato', 'capao-news'); ?> &rarr;</a>
            </div>
        </div>
        <div class="footer-bottom"><span>&copy; <?php echo esc_html(wp_date('Y')); ?> Capão News</span><span><?php esc_html_e('Desenvolvido por Bruno Dantas', 'capao-news'); ?></span></div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
