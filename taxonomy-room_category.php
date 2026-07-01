<?php get_header(); ?>

<section class="rooms-section">
    <div class="container">

        <?php if (is_tax('room_category')) :
            $current_term = get_queried_object();
            $cat_image = get_term_meta($current_term->term_id, 'category_image', true);
            $cat_embed = get_term_meta($current_term->term_id, 'category_chat_embed', true);
        ?>

        <!-- BREADCRUMB -->
        <nav class="chat-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
            <span>/</span>
            <span class="current"><?php single_term_title(); ?></span>
        </nav>

        <!-- CATEGORY HEADER -->
        <div class="category-header">
            <?php if ($cat_image) : ?>
                <div class="category-header-image">
                    <img src="<?php echo esc_url($cat_image); ?>" alt="<?php single_term_title(); ?>">
                </div>
            <?php endif; ?>
            <div class="category-header-info">
                <h1>Canales en la categoria <?php single_term_title(); ?></h1>
                <?php if (term_description()) : ?>
                    <div class="category-header-desc"><?php echo term_description(); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- CATEGORY CHAT EMBED -->
        <?php
        $embed_code = $cat_embed ? $cat_embed : get_theme_mod('xat_embed_code', '');
        if ($embed_code) :
        ?>
        <div class="category-chat-box">
            <div class="category-chat-label">
                <span><?php single_term_title(); ?></span>
            </div>
            <div class="chat-embed-wrapper">
                <?php echo $embed_code; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php else : ?>

        <h1 class="section-title">Todos los Canales de TV</h1>
        <p class="section-subtitle">Explora todas nuestras salas disponibles</p>

        <?php endif; ?>

        <!-- FEATURED ROOMS WITH IMAGES (4 columns) -->
        <?php
        $featured_args = array(
            'post_type'      => 'chat_room',
            'posts_per_page' => 4,
            'meta_query'     => array(
                array(
                    'key'   => '_featured_room',
                    'value' => '1',
                ),
            ),
        );

        if (is_tax('room_category')) {
            $featured_args['tax_query'] = array(
                array(
                    'taxonomy' => 'room_category',
                    'field'    => 'term_id',
                    'terms'    => $current_term->term_id,
                ),
            );
        }

        $featured = new WP_Query($featured_args);

        if (!$featured->have_posts()) {
            unset($featured_args['meta_query']);
            $featured = new WP_Query($featured_args);
        }

        if ($featured->have_posts()) :
        ?>
        <h2 class="section-title" style="font-size: 20px; margin-top: 40px; margin-bottom: 20px;">Canales de TV Recomendados</h2>
        <div class="rooms-grid">
            <?php while ($featured->have_posts()) : $featured->the_post();
                $users = get_post_meta(get_the_ID(), '_users_online', true);
            ?>
            <article class="room-card">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('room-thumbnail', array('class' => 'room-card-image')); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php the_permalink(); ?>">
                        <div class="room-card-image" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; font-weight: 700;"><?php echo esc_html(mb_substr(get_the_title(), 0, 2)); ?></div>
                    </a>
                <?php endif; ?>
                <div class="room-card-body">
                    <h3 class="room-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php if (has_excerpt()) : ?>
                        <p class="room-card-desc"><?php echo esc_html(get_the_excerpt()); ?></p>
                    <?php endif; ?>
                    <div class="room-card-meta">
                        <?php if ($users) : ?>
                            <span class="users-online"><?php echo intval($users); ?> en linea</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="room-card-btn" style="margin-top: 12px;">Entrar</a>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>

        <!-- SUBCATEGORIES -->
        <?php
        $subcats = get_terms(array(
            'taxonomy'   => 'room_category',
            'parent'     => $current_term->term_id,
            'hide_empty' => false,
        ));
        if (!is_wp_error($subcats) && !empty($subcats)) :
        ?>
        <div style="margin-top: 40px;">
            <h2 class="section-title" style="font-size: 20px; margin-bottom: 20px;">Sub categorias de <?php single_term_title(); ?></h2>
            <div class="categories-grid">
                <?php foreach ($subcats as $subcat) :
                    $subcat_image = get_term_meta($subcat->term_id, 'category_image', true);
                ?>
                <a href="<?php echo esc_url(get_term_link($subcat)); ?>" class="category-card">
                    <?php if ($subcat_image) : ?>
                        <img src="<?php echo esc_url($subcat_image); ?>" alt="<?php echo esc_attr($subcat->name); ?>" class="category-card-image">
                    <?php else : ?>
                        <div class="category-card-image" style="background: linear-gradient(135deg, var(--primary-light), var(--primary)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 28px; font-weight: 700;"><?php echo esc_html(mb_substr($subcat->name, 0, 2)); ?></div>
                    <?php endif; ?>
                    <div class="category-card-body">
                        <h3 class="category-card-title"><?php echo esc_html($subcat->name); ?></h3>
                        <p class="category-card-count"><?php echo $subcat->count; ?> salas</p>
                        <span class="room-card-btn" style="margin-top: 10px;">Entrar</span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ALL ROOMS AS TEXT LINKS -->
        <?php
        $all_args = array(
            'post_type'      => 'chat_room',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        );

        if (is_tax('room_category')) {
            $all_args['tax_query'] = array(
                array(
                    'taxonomy' => 'room_category',
                    'field'    => 'term_id',
                    'terms'    => $current_term->term_id,
                ),
            );
        }

        $all_rooms = new WP_Query($all_args);

        if ($all_rooms->have_posts()) :
        ?>
        <div class="all-rooms-section" style="margin-top: 40px;">
            <h2 class="section-title" style="font-size: 20px; margin-bottom: 20px;">Todos los Canales</h2>
            <div class="all-rooms-links">
                <?php while ($all_rooms->have_posts()) : $all_rooms->the_post(); ?>
                    <a href="<?php the_permalink(); ?>" class="room-link"><?php the_title(); ?></a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
