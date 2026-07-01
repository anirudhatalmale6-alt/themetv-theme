<?php if (is_active_sidebar('sidebar-1')) : ?>
    <?php dynamic_sidebar('sidebar-1'); ?>
<?php else : ?>
    <div class="widget">
        <h3 class="widget-title">Categorias</h3>
        <ul>
            <?php
            $cats = get_terms(array(
                'taxonomy'   => 'room_category',
                'hide_empty' => false,
            ));
            if (!is_wp_error($cats)) :
                foreach ($cats as $cat) :
            ?>
                <li style="padding: 6px 0; border-bottom: 1px solid var(--border);">
                    <a href="<?php echo esc_url(get_term_link($cat)); ?>" style="color: var(--text); display: flex; justify-content: space-between;">
                        <?php echo esc_html($cat->name); ?>
                        <span style="color: var(--text-muted);">(<?php echo $cat->count; ?>)</span>
                    </a>
                </li>
            <?php
                endforeach;
            endif;
            ?>
        </ul>
    </div>
    <div class="widget">
        <h3 class="widget-title">Salas Populares</h3>
        <ul>
            <?php
            $pop = new WP_Query(array(
                'post_type'      => 'chat_room',
                'posts_per_page' => 5,
                'orderby'        => 'comment_count',
                'order'          => 'DESC',
            ));
            if ($pop->have_posts()) :
                while ($pop->have_posts()) : $pop->the_post();
            ?>
                <li style="padding: 6px 0; border-bottom: 1px solid var(--border);">
                    <a href="<?php the_permalink(); ?>" style="color: var(--text);"><?php the_title(); ?></a>
                </li>
            <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </ul>
    </div>
<?php endif; ?>
