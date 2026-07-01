<?php get_header(); ?>

<div class="container" style="padding: 40px 20px;">
    <h1 class="section-title">Resultados para: "<?php echo esc_html(get_search_query()); ?>"</h1>

    <?php if (have_posts()) : ?>
    <div class="rooms-grid" style="grid-template-columns: repeat(2, 1fr); margin-top: 30px;">
        <?php while (have_posts()) : the_post(); ?>
        <article class="news-card">
            <?php if (has_post_thumbnail()) : ?>
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('news-thumbnail', array('class' => 'news-card-image')); ?>
                </a>
            <?php endif; ?>
            <div class="news-card-body">
                <span class="news-card-date"><?php echo get_post_type_object(get_post_type())->labels->singular_name; ?> &bull; <?php echo get_the_date(); ?></span>
                <h3 class="news-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p class="news-card-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
    <div class="pagination">
        <?php the_posts_pagination(array('mid_size' => 2, 'prev_text' => '&laquo;', 'next_text' => '&raquo;')); ?>
    </div>
    <?php else : ?>
        <p style="margin-top: 20px;">No se encontraron resultados. Intenta con otra busqueda.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
