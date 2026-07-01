<?php
/**
 * ChatJovenes Theme Functions
 */

if (!defined('ABSPATH')) exit;

// License validation
function chatjovenes_check_license() {
    $allowed_domains = get_option('chatjovenes_licensed_domains', array());
    $current_domain = str_replace('www.', '', parse_url(home_url(), PHP_URL_HOST));
    if (!empty($allowed_domains) && !in_array($current_domain, $allowed_domains)) {
        return false;
    }
    return true;
}

// Theme setup
function chatjovenes_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('automatic-feed-links');

    add_image_size('room-thumbnail', 400, 250, true);
    add_image_size('category-thumbnail', 300, 200, true);
    add_image_size('news-thumbnail', 400, 220, true);

    register_nav_menus(array(
        'primary' => 'Menu Principal',
        'footer'  => 'Menu Footer',
    ));
}
add_action('after_setup_theme', 'chatjovenes_setup');

// Enqueue styles and scripts
function chatjovenes_enqueue() {
    wp_enqueue_style('chatjovenes-style', get_stylesheet_uri(), array(), '2.0.0');
    wp_enqueue_script('chatjovenes-script', get_template_directory_uri() . '/js/main.js', array(), '2.0.0', true);
}
add_action('wp_enqueue_scripts', 'chatjovenes_enqueue');

// Register sidebars
function chatjovenes_widgets() {
    register_sidebar(array(
        'name'          => 'Sidebar',
        'id'            => 'sidebar-1',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    register_sidebar(array(
        'name'          => 'Footer 1',
        'id'            => 'footer-1',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));
    register_sidebar(array(
        'name'          => 'Footer 2',
        'id'            => 'footer-2',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));
    register_sidebar(array(
        'name'          => 'Footer 3',
        'id'            => 'footer-3',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));
    register_sidebar(array(
        'name'          => 'Footer 4',
        'id'            => 'footer-4',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'chatjovenes_widgets');

// Custom Post Type: Chat Rooms
function chatjovenes_register_cpt() {
    register_post_type('chat_room', array(
        'labels' => array(
            'name'               => 'Salas de Chat',
            'singular_name'      => 'Sala de Chat',
            'add_new'            => 'Agregar Sala',
            'add_new_item'       => 'Agregar Nueva Sala',
            'edit_item'          => 'Editar Sala',
            'new_item'           => 'Nueva Sala',
            'view_item'          => 'Ver Sala',
            'search_items'       => 'Buscar Salas',
            'not_found'          => 'No se encontraron salas',
            'not_found_in_trash' => 'No se encontraron salas en la papelera',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'canal'),
        'supports'     => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon'    => 'dashicons-format-chat',
        'show_in_rest' => true,
    ));

    register_taxonomy('room_tag', 'chat_room', array(
        'labels' => array(
            'name'          => 'Etiquetas',
            'singular_name' => 'Etiqueta',
            'add_new_item'  => 'Agregar Etiqueta',
            'search_items'  => 'Buscar Etiquetas',
        ),
        'public'       => true,
        'hierarchical' => false,
        'rewrite'      => array('slug' => 'canal-tag'),
        'show_in_rest' => true,
    ));

    register_taxonomy('room_category', 'chat_room', array(
        'labels' => array(
            'name'          => 'Categorias',
            'singular_name' => 'Categoria',
            'add_new_item'  => 'Agregar Categoria',
            'search_items'  => 'Buscar Categorias',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array('slug' => 'canales'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'chatjovenes_register_cpt');

// Chat Room meta boxes
function chatjovenes_room_meta_boxes() {
    add_meta_box(
        'chatjovenes_room_details',
        'Detalles de la Sala',
        'chatjovenes_room_meta_callback',
        'chat_room',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'chatjovenes_room_meta_boxes');

function chatjovenes_room_meta_callback($post) {
    wp_nonce_field('chatjovenes_room_meta', 'chatjovenes_room_nonce');
    $xat_embed = get_post_meta($post->ID, '_xat_embed_code', true);
    $users_online = get_post_meta($post->ID, '_users_online', true);
    $featured = get_post_meta($post->ID, '_featured_room', true);
    $hide_title = get_post_meta($post->ID, '_hide_title', true);
    $hide_excerpt = get_post_meta($post->ID, '_hide_excerpt', true);
    $show_radio = get_post_meta($post->ID, '_show_radio', true);
    $radio_embed = get_post_meta($post->ID, '_radio_embed_code', true);
    $radio_url = get_post_meta($post->ID, '_radio_stream_url', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="xat_embed_code">Embed del Chat (iframe)</label></th>
            <td>
                <textarea id="xat_embed_code" name="xat_embed_code" rows="4" class="large-text code"><?php echo esc_textarea($xat_embed); ?></textarea>
                <p class="description">Pega aqui el codigo iframe de tu chat xat.com. Ejemplo: &lt;iframe src="https://xat.com/embed/chat.php#id=31449413&amp;gn=jovenes019" width="650" height="486" frameborder="0" scrolling="no"&gt;&lt;/iframe&gt;</p>
            </td>
        </tr>
        <tr>
            <th><label for="users_online">Usuarios en linea</label></th>
            <td>
                <input type="number" id="users_online" name="users_online" value="<?php echo esc_attr($users_online); ?>" class="small-text">
                <p class="description">Numero estimado de usuarios (se muestra en la tarjeta)</p>
            </td>
        </tr>
        <tr>
            <th><label for="featured_room">Sala Destacada</label></th>
            <td>
                <label>
                    <input type="checkbox" id="featured_room" name="featured_room" value="1" <?php checked($featured, '1'); ?>>
                    Mostrar en la pagina de inicio
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="hide_title">Ocultar Titulo</label></th>
            <td>
                <label>
                    <input type="checkbox" id="hide_title" name="hide_title" value="1" <?php checked($hide_title, '1'); ?>>
                    No mostrar el titulo en la pagina de la sala
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="hide_excerpt">Ocultar Extracto</label></th>
            <td>
                <label>
                    <input type="checkbox" id="hide_excerpt" name="hide_excerpt" value="1" <?php checked($hide_excerpt, '1'); ?>>
                    No mostrar el extracto en las tarjetas
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="show_radio">Mostrar Radio</label></th>
            <td>
                <label>
                    <input type="checkbox" id="show_radio" name="show_radio" value="1" <?php checked($show_radio, '1'); ?>>
                    Mostrar reproductor de radio debajo del chat
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="radio_embed_code">Radio - Codigo Embed</label></th>
            <td>
                <textarea id="radio_embed_code" name="radio_embed_code" rows="3" class="large-text code"><?php echo esc_textarea($radio_embed); ?></textarea>
                <p class="description">Pega aqui el codigo embed/iframe del reproductor de radio (tiene prioridad sobre la URL)</p>
            </td>
        </tr>
        <tr>
            <th><label for="radio_stream_url">Radio - URL del Streaming</label></th>
            <td>
                <input type="url" id="radio_stream_url" name="radio_stream_url" value="<?php echo esc_attr($radio_url); ?>" class="large-text">
                <p class="description">URL directa del streaming de radio (se usa si no hay codigo embed)</p>
            </td>
        </tr>
    </table>
    <?php
}

function chatjovenes_save_room_meta($post_id) {
    if (!isset($_POST['chatjovenes_room_nonce']) || !wp_verify_nonce($_POST['chatjovenes_room_nonce'], 'chatjovenes_room_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['xat_embed_code'])) {
        update_post_meta($post_id, '_xat_embed_code', chatjovenes_sanitize_embed($_POST['xat_embed_code']));
    }
    if (isset($_POST['users_online'])) {
        update_post_meta($post_id, '_users_online', intval($_POST['users_online']));
    }
    update_post_meta($post_id, '_featured_room', isset($_POST['featured_room']) ? '1' : '0');
    update_post_meta($post_id, '_hide_title', isset($_POST['hide_title']) ? '1' : '0');
    update_post_meta($post_id, '_hide_excerpt', isset($_POST['hide_excerpt']) ? '1' : '0');
    update_post_meta($post_id, '_show_radio', isset($_POST['show_radio']) ? '1' : '0');
    if (isset($_POST['radio_embed_code'])) {
        update_post_meta($post_id, '_radio_embed_code', chatjovenes_sanitize_embed($_POST['radio_embed_code']));
    }
    if (isset($_POST['radio_stream_url'])) {
        update_post_meta($post_id, '_radio_stream_url', esc_url_raw($_POST['radio_stream_url']));
    }
}
add_action('save_post_chat_room', 'chatjovenes_save_room_meta');

// Category image field
function chatjovenes_category_add_image($taxonomy) {
    ?>
    <div class="form-field">
        <label for="category_image">Imagen de Categoria</label>
        <input type="text" name="category_image" id="category_image" value="">
        <p class="description">URL de la imagen para esta categoria (o usa el boton de medios)</p>
        <button type="button" class="button chatjovenes-upload-btn" data-target="category_image">Subir Imagen</button>
    </div>
    <div class="form-field">
        <label for="category_chat_embed">Embed del Chat (iframe)</label>
        <textarea name="category_chat_embed" id="category_chat_embed" rows="3"></textarea>
        <p class="description">Pega aqui el codigo iframe del chat para esta categoria</p>
    </div>
    <?php
}
add_action('room_category_add_form_fields', 'chatjovenes_category_add_image');

function chatjovenes_category_edit_image($term) {
    $image = get_term_meta($term->term_id, 'category_image', true);
    $chat_embed = get_term_meta($term->term_id, 'category_chat_embed', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="category_image">Imagen de Categoria</label></th>
        <td>
            <input type="text" name="category_image" id="category_image" value="<?php echo esc_attr($image); ?>">
            <p class="description">URL de la imagen para esta categoria</p>
            <button type="button" class="button chatjovenes-upload-btn" data-target="category_image">Subir Imagen</button>
            <?php if ($image) : ?>
                <br><img src="<?php echo esc_url($image); ?>" style="max-width: 200px; margin-top: 10px;">
            <?php endif; ?>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="category_chat_embed">Embed del Chat (iframe)</label></th>
        <td>
            <textarea name="category_chat_embed" id="category_chat_embed" rows="4" class="large-text code"><?php echo esc_textarea($chat_embed); ?></textarea>
            <p class="description">Pega aqui el codigo iframe del chat para esta categoria</p>
        </td>
    </tr>
    <?php
}
add_action('room_category_edit_form_fields', 'chatjovenes_category_edit_image');

function chatjovenes_save_category_image($term_id) {
    if (isset($_POST['category_image'])) {
        update_term_meta($term_id, 'category_image', esc_url_raw($_POST['category_image']));
    }
    if (isset($_POST['category_chat_embed'])) {
        update_term_meta($term_id, 'category_chat_embed', chatjovenes_sanitize_embed($_POST['category_chat_embed']));
    }
}
add_action('created_room_category', 'chatjovenes_save_category_image');
add_action('edited_room_category', 'chatjovenes_save_category_image');

function chatjovenes_category_admin_scripts($hook) {
    if ($hook !== 'edit-tags.php' && $hook !== 'term.php') return;
    if (!isset($_GET['taxonomy']) || $_GET['taxonomy'] !== 'room_category') return;
    wp_enqueue_media();
    wp_add_inline_script('jquery-core', '
        jQuery(document).ready(function($){
            $(document).on("click", ".chatjovenes-upload-btn", function(e){
                e.preventDefault();
                var target = $(this).data("target");
                var frame = wp.media({title: "Seleccionar Imagen", button: {text: "Usar Imagen"}, multiple: false});
                frame.on("select", function(){
                    var url = frame.state().get("selection").first().toJSON().url;
                    $("#" + target).val(url);
                });
                frame.open();
            });
        });
    ');
}
add_action('admin_enqueue_scripts', 'chatjovenes_category_admin_scripts');

// Theme Customizer
function chatjovenes_customizer($wp_customize) {
    // Hero Section
    $wp_customize->add_section('chatjovenes_hero', array(
        'title'    => 'Seccion Hero',
        'priority' => 30,
    ));

    $wp_customize->add_setting('hero_title', array(
        'default'           => 'Bienvenido a ChatJovenes',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_title', array(
        'label'   => 'Titulo Hero',
        'section' => 'chatjovenes_hero',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('hero_subtitle', array(
        'default'           => 'Conecta con personas de todo el mundo hispano',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_subtitle', array(
        'label'   => 'Subtitulo Hero',
        'section' => 'chatjovenes_hero',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('hero_button_text', array(
        'default'           => 'Conectar',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_button_text', array(
        'label'   => 'Texto del Boton',
        'section' => 'chatjovenes_hero',
        'type'    => 'text',
    ));

    // Homepage Layout
    $wp_customize->add_section('chatjovenes_homepage', array(
        'title'    => 'Pagina de Inicio',
        'priority' => 31,
    ));

    $wp_customize->add_setting('homepage_categories_limit', array(
        'default'           => 8,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('homepage_categories_limit', array(
        'label'       => 'Numero de categorias a mostrar',
        'description' => 'Limite de categorias en la pagina de inicio (0 = todas)',
        'section'     => 'chatjovenes_homepage',
        'type'        => 'number',
        'input_attrs' => array('min' => 0, 'max' => 50, 'step' => 1),
    ));

    $wp_customize->add_setting('homepage_rooms_limit', array(
        'default'           => 8,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('homepage_rooms_limit', array(
        'label'       => 'Numero de salas destacadas a mostrar',
        'description' => 'Limite de salas recomendadas en la pagina de inicio',
        'section'     => 'chatjovenes_homepage',
        'type'        => 'number',
        'input_attrs' => array('min' => 1, 'max' => 50, 'step' => 1),
    ));

    // Header Options
    $wp_customize->add_setting('hide_site_title', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('hide_site_title', array(
        'label'   => 'Ocultar Titulo del Sitio',
        'section' => 'title_tagline',
        'type'    => 'checkbox',
    ));

    $wp_customize->add_setting('hide_site_description', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('hide_site_description', array(
        'label'   => 'Ocultar Descripcion del Sitio',
        'section' => 'title_tagline',
        'type'    => 'checkbox',
    ));

    // Xat Chat Settings
    $wp_customize->add_section('chatjovenes_xat', array(
        'title'    => 'Chat xat.com',
        'priority' => 35,
    ));

    $wp_customize->add_setting('xat_group_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('xat_group_id', array(
        'label'       => 'ID del Grupo xat',
        'description' => 'El ID numerico de tu grupo en xat.com',
        'section'     => 'chatjovenes_xat',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('xat_show_homepage', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('xat_show_homepage', array(
        'label'   => 'Mostrar chat en la pagina de inicio',
        'section' => 'chatjovenes_xat',
        'type'    => 'checkbox',
    ));

    // Xat Embed Code
    $wp_customize->add_setting('xat_embed_code', array(
        'default'           => '',
        'sanitize_callback' => 'chatjovenes_sanitize_embed',
    ));
    $wp_customize->add_control('xat_embed_code', array(
        'label'       => 'Codigo Embed de xat',
        'description' => 'Pega aqui el codigo embed completo de tu chat xat.com (tiene prioridad sobre el ID de grupo)',
        'section'     => 'chatjovenes_xat',
        'type'        => 'textarea',
    ));

    // Header Buttons
    $wp_customize->add_section('chatjovenes_header_buttons', array(
        'title'    => 'Botones del Header',
        'priority' => 33,
    ));

    $wp_customize->add_setting('chat_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('chat_url', array(
        'label'       => 'URL Entrar al Chat',
        'description' => 'Enlace del boton Entrar al Chat',
        'section'     => 'chatjovenes_header_buttons',
        'type'        => 'url',
    ));

    $wp_customize->add_setting('facebook_group_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('facebook_group_url', array(
        'label'   => 'URL Grupo Facebook',
        'section' => 'chatjovenes_header_buttons',
        'type'    => 'url',
    ));

    $wp_customize->add_setting('whatsapp_group_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('whatsapp_group_url', array(
        'label'   => 'URL Grupo WhatsApp',
        'section' => 'chatjovenes_header_buttons',
        'type'    => 'url',
    ));

    // Radio Button
    $wp_customize->add_section('chatjovenes_radio', array(
        'title'    => 'Boton Radio',
        'priority' => 36,
    ));

    $wp_customize->add_setting('radio_url', array(
        'default'           => 'https://holaxat.com/radio/player',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('radio_url', array(
        'label'       => 'URL de la Radio',
        'description' => 'Enlace que se abre en la ventana popup',
        'section'     => 'chatjovenes_radio',
        'type'        => 'url',
    ));

    $wp_customize->add_setting('radio_button_text', array(
        'default'           => 'Escuchar La Radio',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('radio_button_text', array(
        'label'   => 'Texto del Boton',
        'section' => 'chatjovenes_radio',
        'type'    => 'text',
    ));

    // Global Radio Bar
    $wp_customize->add_section('chatjovenes_radio_bar', array(
        'title'    => 'Barra de Radio Global',
        'priority' => 37,
    ));

    $wp_customize->add_setting('radio_bar_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('radio_bar_enabled', array(
        'label'       => 'Activar barra de radio global',
        'description' => 'Muestra una barra fija con radio en toda la web',
        'section'     => 'chatjovenes_radio_bar',
        'type'        => 'checkbox',
    ));

    $wp_customize->add_setting('radio_bar_embed', array(
        'default'           => '',
        'sanitize_callback' => 'chatjovenes_sanitize_embed',
    ));
    $wp_customize->add_control('radio_bar_embed', array(
        'label'       => 'Codigo Embed del reproductor',
        'description' => 'Pega el iframe/embed del reproductor de radio (tiene prioridad sobre la URL)',
        'section'     => 'chatjovenes_radio_bar',
        'type'        => 'textarea',
    ));

    $wp_customize->add_setting('radio_bar_stream_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('radio_bar_stream_url', array(
        'label'       => 'URL del Streaming',
        'description' => 'URL directa del streaming (se usa si no hay codigo embed)',
        'section'     => 'chatjovenes_radio_bar',
        'type'        => 'url',
    ));

    $wp_customize->add_setting('radio_bar_bg_color', array(
        'default'           => '#1e293b',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'radio_bar_bg_color', array(
        'label'   => 'Color de fondo de la barra',
        'section' => 'chatjovenes_radio_bar',
    )));

    $wp_customize->add_setting('radio_bar_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'radio_bar_text_color', array(
        'label'   => 'Color de texto de la barra',
        'section' => 'chatjovenes_radio_bar',
    )));

    // Colors
    $wp_customize->add_setting('primary_color', array(
        'default'           => '#2563eb',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label'   => 'Color Primario',
        'section' => 'colors',
    )));

    $wp_customize->add_setting('accent_color', array(
        'default'           => '#f97316',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'accent_color', array(
        'label'   => 'Color de Acento',
        'section' => 'colors',
    )));

    $wp_customize->add_setting('bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'bg_color', array(
        'label'   => 'Color de Fondo',
        'section' => 'colors',
    )));

    $wp_customize->add_setting('text_color', array(
        'default'           => '#1e293b',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'text_color', array(
        'label'   => 'Color del Texto',
        'section' => 'colors',
    )));

    $wp_customize->add_setting('card_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'card_bg_color', array(
        'label'   => 'Color de Fondo de Tarjetas',
        'section' => 'colors',
    )));

    $wp_customize->add_setting('footer_bg_color', array(
        'default'           => '#0f172a',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_bg_color', array(
        'label'   => 'Color de Fondo del Footer',
        'section' => 'colors',
    )));

    // Dark Mode
    $wp_customize->add_section('chatjovenes_darkmode', array(
        'title'    => 'Modo Oscuro',
        'priority' => 32,
    ));

    $wp_customize->add_setting('enable_dark_toggle', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('enable_dark_toggle', array(
        'label'       => 'Mostrar boton de Modo Oscuro',
        'description' => 'Los usuarios podran cambiar entre modo claro y oscuro',
        'section'     => 'chatjovenes_darkmode',
        'type'        => 'checkbox',
    ));

    $wp_customize->add_setting('default_dark_mode', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('default_dark_mode', array(
        'label'   => 'Activar modo oscuro por defecto',
        'section' => 'chatjovenes_darkmode',
        'type'    => 'checkbox',
    ));

    $wp_customize->add_setting('enable_scroll_top', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('enable_scroll_top', array(
        'label'       => 'Mostrar boton Ir Arriba',
        'description' => 'Muestra el boton flotante para ir arriba al hacer scroll',
        'section'     => 'chatjovenes_darkmode',
        'type'        => 'checkbox',
    ));

    // Social Media
    $wp_customize->add_section('chatjovenes_social', array(
        'title'    => 'Redes Sociales',
        'priority' => 40,
    ));

    foreach (array('facebook' => 'Facebook', 'twitter' => 'Twitter/X', 'instagram' => 'Instagram', 'youtube' => 'YouTube') as $key => $label) {
        $wp_customize->add_setting("social_$key", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("social_$key", array(
            'label'   => $label . ' URL',
            'section' => 'chatjovenes_social',
            'type'    => 'url',
        ));
    }

    // Normas de la Sala
    $wp_customize->add_section('chatjovenes_normas', array(
        'title'    => 'Normas de la Sala',
        'priority' => 45,
    ));

    $wp_customize->add_setting('normas_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('normas_enabled', array(
        'label'   => 'Mostrar seccion de Normas',
        'section' => 'chatjovenes_normas',
        'type'    => 'checkbox',
    ));

    $wp_customize->add_setting('normas_title', array(
        'default'           => 'Normas de la sala',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('normas_title', array(
        'label'   => 'Titulo',
        'section' => 'chatjovenes_normas',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('normas_subtitle', array(
        'default'           => 'Reglas claras, conversacion sana.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('normas_subtitle', array(
        'label'   => 'Subtitulo',
        'section' => 'chatjovenes_normas',
        'type'    => 'text',
    ));

    for ($i = 1; $i <= 4; $i++) {
        $defaults_title = array(
            1 => 'Sin registro, pero con respeto.',
            2 => 'No compartas datos de terceros.',
            3 => 'Se educado con la moderacion.',
            4 => 'Spam, insultos y contenido sexual explicito = ban.',
        );
        $defaults_text = array(
            1 => 'Es una sala publica. Trata a los demas como te gustaria que te tratasen a ti.',
            2 => 'Direcciones, telefonos o informacion personal de otras personas — esta prohibido.',
            3 => 'Los moderadores estan para ayudarte. Si tienes una duda o un problema, escribeles privado.',
            4 => 'Las salas son para conocer gente, no para promocionar ni para faltar al respeto.',
        );

        $wp_customize->add_setting("normas_rule_{$i}_title", array(
            'default'           => $defaults_title[$i],
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("normas_rule_{$i}_title", array(
            'label'   => "Regla {$i} - Titulo",
            'section' => 'chatjovenes_normas',
            'type'    => 'text',
        ));

        $wp_customize->add_setting("normas_rule_{$i}_text", array(
            'default'           => $defaults_text[$i],
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("normas_rule_{$i}_text", array(
            'label'   => "Regla {$i} - Texto",
            'section' => 'chatjovenes_normas',
            'type'    => 'textarea',
        ));
    }

    $wp_customize->add_setting('normas_box_title', array(
        'default'           => 'Servicio gratuito, siempre',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('normas_box_title', array(
        'label'   => 'Cuadro destacado - Titulo',
        'section' => 'chatjovenes_normas',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('normas_box_text', array(
        'default'           => 'Nuestro servicio de chat es gratuito y lo sera siempre. Sin email, sin telefono, sin rastro. Si ves algo que no deberia estar pasando, avisa a un moderador desde el propio chat.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('normas_box_text', array(
        'label'   => 'Cuadro destacado - Texto',
        'section' => 'chatjovenes_normas',
        'type'    => 'textarea',
    ));

    // License
    $wp_customize->add_section('chatjovenes_license', array(
        'title'    => 'Licencia',
        'priority' => 200,
    ));

    $wp_customize->add_setting('license_domains', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('license_domains', array(
        'label'       => 'Dominios Autorizados',
        'description' => 'Un dominio por linea (sin www). Dejar vacio para sin restriccion.',
        'section'     => 'chatjovenes_license',
        'type'        => 'textarea',
    ));
}
add_action('customize_register', 'chatjovenes_customizer');

// Sanitize embed code
function chatjovenes_sanitize_embed($input) {
    return wp_kses($input, array(
        'iframe' => array('src' => true, 'width' => true, 'height' => true, 'allowfullscreen' => true, 'style' => true, 'frameborder' => true, 'scrolling' => true, 'allow' => true),
        'embed'  => array('src' => true, 'width' => true, 'height' => true, 'type' => true),
        'object' => array('data' => true, 'width' => true, 'height' => true, 'type' => true),
        'param'  => array('name' => true, 'value' => true),
        'script' => array('src' => true, 'type' => true),
        'div'    => array('id' => true, 'class' => true, 'style' => true),
    ));
}

// Dynamic CSS from customizer
function chatjovenes_dynamic_css() {
    $primary = get_theme_mod('primary_color', '#2563eb');
    $accent = get_theme_mod('accent_color', '#f97316');
    $bg = get_theme_mod('bg_color', '#ffffff');
    $text = get_theme_mod('text_color', '#1e293b');
    $card_bg = get_theme_mod('card_bg_color', '#ffffff');
    $footer_bg = get_theme_mod('footer_bg_color', '#0f172a');
    ?>
    <style>
        :root {
            --primary: <?php echo esc_attr($primary); ?>;
            --accent: <?php echo esc_attr($accent); ?>;
            --bg: <?php echo esc_attr($bg); ?>;
            --text: <?php echo esc_attr($text); ?>;
            --bg-light: <?php echo esc_attr($bg); ?>;
            --card-bg: <?php echo esc_attr($card_bg); ?>;
        }
        body { background-color: var(--bg); color: var(--text); }
        .room-card, .news-card, .category-card, .sidebar .widget { background: var(--card-bg); }
        .site-header { background: var(--bg); }
        .site-footer { background: <?php echo esc_attr($footer_bg); ?>; }

        /* Dark mode styles */
        body.dark-mode {
            --bg: #121212;
            --bg-light: #1a1a2e;
            --bg-section: #1e1e30;
            --text: #e2e8f0;
            --text-light: #a0aec0;
            --text-muted: #718096;
            --border: #2d3748;
            --card-bg: #1e293b;
        }
        body.dark-mode .site-header { background: #0f172a; border-color: #1e293b; }
        body.dark-mode .main-nav a { color: #e2e8f0; }
        body.dark-mode .main-nav a:hover { background: #1e293b; }
        body.dark-mode .site-logo { color: #e2e8f0; }
        body.dark-mode .room-card, body.dark-mode .news-card, body.dark-mode .category-card { background: #1e293b; }
        body.dark-mode .room-card-title, body.dark-mode .news-card-title, body.dark-mode .category-card-title { color: #e2e8f0; }
        body.dark-mode .room-card-title a, body.dark-mode .news-card-title a, body.dark-mode .category-card-title a { color: #e2e8f0; }
        body.dark-mode .categories-section { background: #0f172a; }
        body.dark-mode .channels-section { background: #0f172a; }
        body.dark-mode .channels-column h3 { color: #e2e8f0; }
        body.dark-mode .channels-list a { color: #cbd5e1; }
        body.dark-mode .channels-list li { border-color: #2d3748; }
        body.dark-mode .channel-badge { background: #2d3748; color: #a0aec0; }
        body.dark-mode .section-title { color: #e2e8f0; }
        body.dark-mode .connect-form input[type="text"] { background: rgba(255,255,255,0.1); color: #fff; }
        body.dark-mode .sidebar .widget { background: #1e293b; }
        body.dark-mode .sidebar .widget-title { color: #e2e8f0; }

        /* Dark mode toggle button */
        .dark-mode-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 22px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s, transform 0.2s;
        }
        .dark-mode-toggle:hover {
            transform: scale(1.1);
        }
        body.dark-mode .dark-mode-toggle {
            background: #f59e0b;
        }
    </style>
    <?php
}
add_action('wp_head', 'chatjovenes_dynamic_css');

// Excerpt length
function chatjovenes_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'chatjovenes_excerpt_length');

function chatjovenes_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'chatjovenes_excerpt_more');

// Save licensed domains from customizer
function chatjovenes_save_license_domains($value) {
    $domains = array_filter(array_map('trim', explode("\n", $value)));
    update_option('chatjovenes_licensed_domains', $domains);
    return $value;
}
add_filter('pre_set_theme_mod_license_domains', 'chatjovenes_save_license_domains');

// Image sitemap support
add_filter('wp_sitemaps_posts_entry', 'chatjovenes_sitemap_add_images', 10, 3);

function chatjovenes_sitemap_add_images($sitemap_entry, $post, $post_type) {
    if ($post_type !== 'chat_room' && $post_type !== 'post') return $sitemap_entry;

    $images = array();

    if (has_post_thumbnail($post->ID)) {
        $thumb_url = get_the_post_thumbnail_url($post->ID, 'full');
        if ($thumb_url) {
            $images[] = array('loc' => $thumb_url);
        }
    }

    $content = $post->post_content;
    if ($content) {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $img_url) {
                $images[] = array('loc' => $img_url);
            }
        }
    }

    if (!empty($images)) {
        $sitemap_entry['images'] = $images;
    }

    return $sitemap_entry;
}

function chatjovenes_sitemap_add_tax_images($sitemap_entry, $term, $taxonomy) {
    if ($taxonomy !== 'room_category') return $sitemap_entry;

    $cat_image = get_term_meta($term->term_id, 'category_image', true);
    if ($cat_image) {
        $sitemap_entry['images'] = array(array('loc' => $cat_image));
    }

    return $sitemap_entry;
}
add_filter('wp_sitemaps_taxonomies_entry', 'chatjovenes_sitemap_add_tax_images', 10, 3);

// Flush rewrite rules on activation for new permalink slugs
function chatjovenes_activate() {
    chatjovenes_register_cpt();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'chatjovenes_activate');
