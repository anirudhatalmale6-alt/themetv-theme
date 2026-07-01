<?php get_header(); ?>

<div class="container">
    <div class="content-with-sidebar">
        <article class="single-post-content">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <h1><?php the_title(); ?></h1>
                <div class="post-meta">
                    <?php echo get_the_date(); ?> &bull; <?php the_author(); ?>
                    <?php
                    $cats = get_the_category();
                    if ($cats) {
                        echo ' &bull; ';
                        foreach ($cats as $i => $cat) {
                            if ($i > 0) echo ', ';
                            echo '<a href="' . esc_url(get_category_link($cat->term_id)) . '">' . esc_html($cat->name) . '</a>';
                        }
                    }
                    ?>
                </div>
                <?php if (has_post_thumbnail()) : ?>
                    <div style="margin-bottom: 24px;">
                        <?php the_post_thumbnail('large', array('style' => 'border-radius: var(--radius-lg); width: 100%;')); ?>
                    </div>
                <?php endif; ?>
                <div class="post-content">
                    <?php the_content(); ?>
                </div>
                <?php
                the_posts_navigation(array(
                    'prev_text' => '&laquo; %title',
                    'next_text' => '%title &raquo;',
                ));
                ?>
                <?php if (comments_open() || get_comments_number()) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
            <?php endwhile; endif; ?>
        </article>
        <aside class="sidebar">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>

<?php get_footer(); ?>
