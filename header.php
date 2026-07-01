<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="header-inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
            <?php if (has_custom_logo()) : ?>
                <?php
                $logo_id = get_theme_mod('custom_logo');
                $logo_url = wp_get_attachment_image_url($logo_id, 'full');
                ?>
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>">
            <?php endif; ?>
            <?php if (!get_theme_mod('hide_site_title', false)) : ?>
                <span><?php bloginfo('name'); ?></span>
            <?php endif; ?>
            <?php if (!get_theme_mod('hide_site_description', false) && get_bloginfo('description')) : ?>
                <small style="font-size: 12px; font-weight: 400; color: var(--text-light); margin-left: 8px;"><?php bloginfo('description'); ?></small>
            <?php endif; ?>
        </a>

        <button class="menu-toggle" aria-label="Menu" id="menuToggle" onclick="document.getElementById('mainNav').classList.toggle('active')">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="<?php echo esc_url(home_url('/')); ?>" class="header-btn header-btn-inicio">Inicio</a></li>
                <?php
                $chat_url = get_theme_mod('chat_url', '');
                if ($chat_url) :
                ?>
                <li><a href="<?php echo esc_url($chat_url); ?>" class="header-btn header-btn-chat">Ver Canal</a></li>
                <?php endif; ?>
                <?php
                $fb_url = get_theme_mod('facebook_group_url', '');
                if ($fb_url) :
                ?>
                <li><a href="<?php echo esc_url($fb_url); ?>" class="header-btn header-btn-facebook" target="_blank" rel="noopener">Grupo Facebook</a></li>
                <?php endif; ?>
                <?php
                $wa_url = get_theme_mod('whatsapp_group_url', '');
                if ($wa_url) :
                ?>
                <li><a href="<?php echo esc_url($wa_url); ?>" class="header-btn header-btn-whatsapp" target="_blank" rel="noopener">Grupo WhatsApp</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <?php
        $radio_url = get_theme_mod('radio_url', 'https://holaxat.com/radio/player');
        $radio_text = get_theme_mod('radio_button_text', 'Escuchar La Radio');
        if ($radio_url) :
        ?>
        <a href="<?php echo esc_url($radio_url); ?>" class="radio-btn" onclick="window.open(this.href, 'radio', 'width=400,height=600,scrollbars=no,resizable=yes'); return false;">
            <?php echo esc_html($radio_text); ?>
        </a>
        <?php endif; ?>
    </div>
</header>
<script>
(function(){
    var btn = document.getElementById('menuToggle');
    var nav = document.getElementById('mainNav');
    if (btn && nav) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            nav.classList.toggle('active');
        });
        document.addEventListener('click', function(e) {
            if (!nav.contains(e.target) && !btn.contains(e.target)) {
                nav.classList.remove('active');
            }
        });
    }
})();
</script>
<?php
function chatjovenes_fallback_menu() {
    $categories = get_terms(array(
        'taxonomy'   => 'room_category',
        'hide_empty' => false,
        'number'     => 8,
    ));
    echo '<ul>';
    echo '<li><a href="' . esc_url(home_url('/')) . '">Inicio</a></li>';
    if (!is_wp_error($categories) && !empty($categories)) {
        foreach ($categories as $cat) {
            echo '<li><a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a></li>';
        }
    }
    echo '</ul>';
}
?>
