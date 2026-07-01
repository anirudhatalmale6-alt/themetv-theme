<?php get_header(); ?>

<div class="container" style="text-align: center; padding: 80px 20px;">
    <h1 style="font-size: 72px; color: var(--primary); margin-bottom: 10px;">404</h1>
    <h2 style="font-size: 28px; margin-bottom: 16px;">Pagina no encontrada</h2>
    <p style="color: var(--text-light); margin-bottom: 30px;">Lo sentimos, la pagina que buscas no existe o fue movida.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-connect" style="display: inline-block; text-decoration: none;">Volver al Inicio</a>
</div>

<?php get_footer(); ?>
