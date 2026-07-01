<?php get_header(); ?>

<div class="container" style="padding: 30px 20px;">
    <?php if (have_posts()) : while (have_posts()) : the_post();
        $xat_embed_room = get_post_meta(get_the_ID(), '_xat_embed_code', true);
        $users = get_post_meta(get_the_ID(), '_users_online', true);
        $room_cats = get_the_terms(get_the_ID(), 'room_category');
        $hide_title = get_post_meta(get_the_ID(), '_hide_title', true);
        $show_radio = get_post_meta(get_the_ID(), '_show_radio', true);
        $radio_embed = get_post_meta(get_the_ID(), '_radio_embed_code', true);
        $radio_stream_url = get_post_meta(get_the_ID(), '_radio_stream_url', true);
    ?>

    <!-- BREADCRUMB -->
    <nav class="chat-breadcrumb">
        <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
        <?php if ($room_cats && !is_wp_error($room_cats)) : ?>
            <span>/</span>
            <a href="<?php echo esc_url(get_term_link($room_cats[0])); ?>"><?php echo esc_html($room_cats[0]->name); ?></a>
        <?php endif; ?>
        <span>/</span>
        <span class="current"><?php the_title(); ?></span>
    </nav>

    <div class="chat-room-layout">
        <div class="chat-room-main">
            <div class="chat-room-header">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="chat-room-thumb">
                        <?php the_post_thumbnail('room-thumbnail'); ?>
                    </div>
                <?php endif; ?>
                <div class="chat-room-info">
                    <?php if (!$hide_title || $hide_title === '0' || $hide_title === '') : ?>
                        <h1>Chat <?php the_title(); ?></h1>
                    <?php endif; ?>
                    <?php
                    $room_excerpt = get_the_excerpt();
                    if ($room_excerpt && trim($room_excerpt) !== '') :
                    ?>
                        <p style="color: var(--text-light); font-size: 15px; line-height: 1.6; margin-top: 8px;"><?php echo esc_html($room_excerpt); ?></p>
                    <?php endif; ?>
                    <?php if ($users) : ?>
                        <div class="post-meta" style="margin-top: 6px;">
                            <span class="users-online"><?php echo intval($users); ?> usuarios en linea</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php
            $global_embed = get_theme_mod('xat_embed_code', '');
            $room_embed = $xat_embed_room ? $xat_embed_room : $global_embed;
            if ($room_embed) :
            ?>
            <div class="category-chat-box">
                <div class="category-chat-label">
                    <span><?php the_title(); ?></span>
                </div>
                <div class="chat-embed-wrapper">
                    <?php echo $room_embed; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($show_radio && $show_radio === '1' && ($radio_embed || $radio_stream_url)) : ?>
            <div class="radio-player-box">
                <div class="radio-player-label">Radio</div>
                <div class="radio-player-content">
                    <?php if ($radio_embed) : ?>
                        <?php echo do_shortcode($radio_embed); ?>
                    <?php elseif ($radio_stream_url) : ?>
                        <audio controls autoplay>
                            <source src="<?php echo esc_url($radio_stream_url); ?>">
                        </audio>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (get_the_content()) : ?>
            <div class="post-content" style="margin-top: 30px; padding: 24px; background: var(--bg-section); border-radius: 12px;">
                <h2 style="font-size: 22px; margin-bottom: 12px;">Descripcion Sala de Chat <?php the_title(); ?></h2>
                <?php the_content(); ?>
            </div>
            <?php endif; ?>

            <!-- RELATED ROOMS BELOW CHAT -->
            <?php
            if ($room_cats && !is_wp_error($room_cats)) :
                $cat_ids = wp_list_pluck($room_cats, 'term_id');
                $related = new WP_Query(array(
                    'post_type'      => 'chat_room',
                    'posts_per_page' => 12,
                    'post__not_in'   => array(get_the_ID()),
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'room_category',
                            'field'    => 'term_id',
                            'terms'    => $cat_ids,
                        ),
                    ),
                ));
                if ($related->have_posts()) :
            ?>
            <section class="related-rooms-section">
                <h2 class="section-title" style="font-size: 20px;">Salas de chat relacionadas:</h2>
                <div class="related-rooms-links">
                    <?php while ($related->have_posts()) : $related->the_post(); ?>
                        <a href="<?php the_permalink(); ?>" class="related-room-link"><?php the_title(); ?></a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
            <?php
                endif;
            endif;
            ?>
        </div>
    </div>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
