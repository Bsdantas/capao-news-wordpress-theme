<?php
/** Optional practical information. Plugin APIs are always guarded. */
if (!function_exists('capao_guia_is_post') || !capao_guia_is_post((int) get_the_ID()) || post_password_required()) {
    return;
}
$values = [];
foreach (capao_guia_fields() as $key => $field) {
    $value = (string) get_post_meta(get_the_ID(), 'capao_guia_' . $key, true);
    if ($value !== '') {
        $values[$key] = ['label' => $field['label'], 'value' => $value];
    }
}
if (!$values) {
    return;
}
?>
<section class="guia-practical" aria-labelledby="guia-practical-title">
    <h2 id="guia-practical-title">Planeje sua visita</h2>
    <dl>
        <?php foreach ($values as $key => $item) : ?>
            <div><dt><?php echo esc_html($item['label']); ?></dt><dd>
                <?php if (in_array($key, ['whatsapp', 'website'], true)) : ?>
                    <a href="<?php echo esc_url($item['value'], ['http', 'https']); ?>" target="_blank" rel="noopener noreferrer"><?php echo $key === 'whatsapp' ? 'Conversar pelo WhatsApp ↗' : 'Visitar site ou rede social ↗'; ?></a>
                <?php elseif ($key === 'phone' && preg_replace('/[^0-9+]/', '', $item['value'])) : ?>
                    <a href="<?php echo esc_url('tel:' . preg_replace('/[^0-9+]/', '', $item['value']), ['tel']); ?>"><?php echo esc_html($item['value']); ?></a>
                <?php else : ?>
                    <?php echo nl2br(esc_html($item['value'])); ?>
                <?php endif; ?>
            </dd></div>
        <?php endforeach; ?>
    </dl>
</section>
