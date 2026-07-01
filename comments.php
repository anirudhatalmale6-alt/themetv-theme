<?php if (post_password_required()) return; ?>

<div id="comments" style="margin-top: 40px;">
    <?php if (have_comments()) : ?>
        <h3 style="font-size: 20px; margin-bottom: 20px;">
            <?php
            printf(
                '%s comentario%s',
                number_format_i18n(get_comments_number()),
                get_comments_number() !== 1 ? 's' : ''
            );
            ?>
        </h3>
        <ol style="list-style: none;">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 40,
            ));
            ?>
        </ol>
        <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply'         => 'Deja un comentario',
        'label_submit'        => 'Enviar',
        'comment_notes_after' => '',
    ));
    ?>
</div>
