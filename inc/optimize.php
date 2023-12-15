<?php
/**
 * Odin optimize functions.
 */

/**
 * Cleanup wp_head().
 */
function odin_head_cleanup()
{
    // Category feeds.
    // remove_action( 'wp_head', 'feed_links_extra', 3 );

    // Post and comment feeds.
    // remove_action( 'wp_head', 'feed_links', 2 );

    // EditURI link.
    remove_action('wp_head', 'rsd_link');

    // Windows live writer.
    remove_action('wp_head', 'wlwmanifest_link');

    // Index link.
    remove_action('wp_head', 'index_rel_link');

    // Previous link.
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);

    // Start link.
    remove_action('wp_head', 'start_post_rel_link', 10, 0);

    // Links for adjacent posts.
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

    // WP version.
    remove_action('wp_head', 'wp_generator');

    // Emoji's
    // remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    // remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    // remove_action( 'wp_print_styles', 'print_emoji_styles' );
    // remove_action( 'admin_print_styles', 'print_emoji_styles' );
    // remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    // remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    // remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}

add_action('init', 'odin_head_cleanup');

/**
 * Remove WP version from RSS.
 */
add_filter('the_generator', '__return_false');

/**
 * Remove injected CSS for recent comments widget.
 */
function odin_remove_wp_widget_recent_comments_style()
{
    if (has_filter('wp_head', 'wp_widget_recent_comments_style')) {
        remove_filter('wp_head', 'wp_widget_recent_comments_style');
    }
}

add_filter('wp_head', 'odin_remove_wp_widget_recent_comments_style', 1);

/**
 * Remove injected CSS from recent comments widget.
 */
function odin_remove_recent_comments_style()
{
    global $wp_widget_factory;

    if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
        remove_action('wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ));
    }
}

add_action('wp_head', 'odin_remove_recent_comments_style', 1);

/**
 * Remove injected CSS from gallery.
 */
add_filter('use_default_gallery_style', '__return_false');

/**
 * Add rel="nofollow" and remove rel="category".
 */
function odin_modify_category_rel($text)
{
    $search = array( 'rel="category"', 'rel="category tag"' );
    $text = str_replace($search, 'rel="nofollow"', $text);

    return $text;
}

add_filter('wp_list_categories', 'odin_modify_category_rel');
add_filter('the_category', 'odin_modify_category_rel');

/**
 * Add rel="nofollow" and remove rel="tag".
 */
function odin_modify_tag_rel($taglink)
{
    return str_replace('rel="tag">', 'rel="nofollow">', $taglink);
}

add_filter('wp_tag_cloud', 'odin_modify_tag_rel');
add_filter('the_tags', 'odin_modify_tag_rel');

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param  array $plugins
 *
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce($plugins)
{
    return is_array($plugins) ? array_diff($plugins, array( 'wpemoji' )) : array();
}

add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');


/**
 * NORK FRAMEWORK optimize functions.
 */

#sanitização de upload de arquivos
add_filter('sanitize_file_name', 'wp_tweaks_clear_file_name');
function wp_tweaks_clear_file_name($filename)
{
    $sanitized_filename = remove_accents($filename); // Convert to ASCII

  // Standard replacements
    $invalid = [
    ' '   => '-',
    '%20' => '-',
    '_'   => '-',
    ];

    $sanitized_filename = str_replace(array_keys($invalid), array_values($invalid), $sanitized_filename);
    $sanitized_filename = preg_replace('/[^A-Za-z0-9-\. ]/', '', $sanitized_filename); // Remove all non-alphanumeric except .
    $sanitized_filename = preg_replace('/\.(?=.*\.)/', '', $sanitized_filename); // Remove all but last .
    $sanitized_filename = preg_replace('/-+/', '-', $sanitized_filename); // Replace any more than one - in a row
    $sanitized_filename = str_replace('-.', '.', $sanitized_filename); // Remove last - if at the end
    $sanitized_filename = strtolower($sanitized_filename); // Lowercase

  /**
   * Apply any more sanitization using this filter
   *
   * @var string $sanitized_filename The sanitized filename
   * @var string $filename           Original filename
   */
    $sanitized_filename = apply_filters('wp_tweaks_sanitize_file_name', $sanitized_filename, $filename);

    return $sanitized_filename;
}


#Disable author pages
add_action('wp', 'wp_tweaks_disable_author_pages');
function wp_tweaks_disable_author_pages()
{
    global $wp_query;
    $disabled = apply_filters('wp_tweaks_disable_author_pages', true);

    if ($disabled && $wp_query->is_author()) {
        $wp_query->set_404();
        status_header(404);
    }
}

# Disable author page and author search by url
add_action('wp', 'wp_tweaks_disable_author_query');
function wp_tweaks_disable_author_query()
{
    global $wp_query;
    $disabled = apply_filters('wp_tweaks_disable_author_query', true);

    if ($disabled && isset($_GET['author'])) {
        $wp_query->set_404();
        status_header(404);
    }
}

#desabilita edição de arquivos no dashboard
add_action('init', 'wp_tweaks_disallow_file_edit');
function wp_tweaks_disallow_file_edit()
{
    if (!defined('DISALLOW_FILE_EDIT')) {
        define('DISALLOW_FILE_EDIT', true);
    }
}


# Disable Emoji Mess
add_action('init', 'wp_tweaks_disable_wp_emojicons');
function wp_tweaks_disable_wp_emojicons()
{
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    add_filter('tiny_mce_plugins', 'wp_tweaks_disable_emojicons_tinymce');
    add_filter('emoji_svg_url', '__return_false');
}

function wp_tweaks_disable_emojicons_tinymce($plugins)
{
    return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
}

#Disable sidebar meta widget
add_action('widgets_init', 'wp_tweaks_disable_meta_widget', 20);
function wp_tweaks_disable_meta_widget()
{
    unregister_widget('WP_Widget_Meta');
}


#Disable "website field" from comment form
add_filter('comment_form_default_fields', 'wp_tweaks_disable_website_field');
function wp_tweaks_disable_website_field($field)
{
    if (isset($field['url'])) {
        unset($field['url']);
    }
    return $field;
}

#Disable wp embed
add_action('wp_footer', 'wp_tweaks_disable_wp_embed_js');
function wp_tweaks_disable_wp_embed_js()
{
    wp_dequeue_script('wp-embed');
}

add_action('init', 'wp_tweaks_disable_oembed', 20);
function wp_tweaks_disable_oembed()
{
  // Remove the REST API endpoint.
    remove_action('rest_api_init', 'wp_oembed_register_route');

  // Turn off oEmbed auto discovery.
    add_filter('embed_oembed_discover', '__return_false');

  // Don't filter oEmbed results.
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

  // Remove oEmbed discovery links.
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

  // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action('wp_head', 'wp_oembed_add_host_js');
    add_filter('tiny_mce_plugins', 'wp_tweaks_disable_embeds_tiny_mce_plugin');

  // Remove all embeds rewrite rules.
    add_filter('rewrite_rules_array', 'wp_tweaks_disable_embeds_rewrites');

  // Remove filter of the oEmbed result before any HTTP requests are made.
    remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
}

function wp_tweaks_disable_embeds_tiny_mce_plugin($plugins)
{
    return array_diff($plugins, ['wpembed']);
}

function wp_tweaks_disable_embeds_rewrites($rules)
{
    foreach ($rules as $rule => $rewrite) {
        if (false !== strpos($rewrite, 'embed=true')) {
            unset($rules[$rule]);
        }
    }
    return $rules;
}


#remover versao do wordpress
function wpb_remove_version()
{
    return '';
}

add_filter('the_generator', 'wpb_remove_version');



#custom painel no feed
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');

function my_custom_dashboard_widgets()
{
    global $wp_meta_boxes;

    wp_add_dashboard_widget('custom_help_widget', 'Nork Digital', 'custom_dashboard_help');
}

function custom_dashboard_help()
{
    echo '<p>Bem-vindo ao seu site! Precisa de ajuda? <a href="mailto:suporte@nork.digital">abrir chamado</a>. Veja alguns tutoriais que podem te ajudar: <a href="https://www.nork.digital/suporte" target="_blank">Help Nork</a></p>';
    echo '<img src="https://www.nork.digital/wp-content/uploads/2017/10/nork-wordpress.png" />';
}
#remover footer
function remove_footer_admin()
{
    echo 'Desenvolvido por <a href="https://www.nork.digital" target="_blank">Nork Digital</a> | Precisa de ajuda? <a href="mailto:suporte@nork.digital" target="_blank">Suporte</a></p>';
}
add_filter('admin_footer_text', 'remove_footer_admin');

#remover feedback de erro wordpress
function no_wordpress_errors()
{
    return 'Usuário ou senha inválido, tente novamente.';
}
add_filter('login_errors', 'no_wordpress_errors');

#remover painel de boas vindas
remove_action('welcome_panel', 'wp_welcome_panel');

//Styling wp-login page
function login_styles()
{
    ?>
<style type="text/css">
body {
  background: #e8b800 !important;
  /* Old browsers */
  background: -moz-linear-gradient(45deg, #e8b800 0%, #fce48a 100%) !important;
  /* FF3.6-15 */
  background: -webkit-linear-gradient(45deg, #e8b800 0%, #fce48a 100%) !important;
  /* Chrome10-25,Safari5.1-6 */
  background: linear-gradient(45deg, #e8b800 0%, #fce48a 100%) !important;
  /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
  filter: progid: DXImageTransform.Microsoft.gradient(startColorstr='#e8b800', endColorstr='#000', GradientType=1);
  /* IE6-9 fallback on horizontal gradient */
  background-attachment: fixed !important;
}

#wp-submit {
  border: none !important;
  box-shadow: none !important;
  background: #000 !important;
  text-shadow: none !important;
  border-radius: 4px !important;
  -webkit-border-radius: 4px !important;
  color: #fff !important;
  display: block;
  width: 100% !important;
  margin: 30px 0 0 0 !important;
  font-size: 16px;
  padding: 5px 0 !important;
  height: auto !important;
  transition: all 0.5s;
}

#wp-submit:hover {
  background: #e8b800 !important;
}

.login h1 a {
  background-image: url('http://bbcreative.org/upload/imagens/889e3e8621c81383c4e5684e96a77049.png') !important;
  background-image: url('http://bbcreative.org/upload/imagens/889e3e8621c81383c4e5684e96a77049.png') !important;
  background-size: 100% !important;
  background-position: center center !important;
  background-repeat: no-repeat;
  height: 74px !important;
  width: 250px !important;
}

.login #backtoblog a,
.login #nav a {
  color: #fff !important;
}
</style>

<?php }
add_action('login_enqueue_scripts', 'login_styles');
// Link logo login
function my_login_logo_url()
{
    return get_bloginfo('url');
}
add_filter('login_headerurl', 'my_login_logo_url');
// Mudar nome ao passar o mouse
function my_login_logo_url_title()
{
    return 'Nork Digital';
}
add_filter('login_headertitle', 'my_login_logo_url_title');


#Remove <link rel="shortlink" ...> from <head>
add_action('after_setup_theme', 'wp_tweaks_remove_shortlink', 20);
function wp_tweaks_remove_shortlink()
{
  // remove HTML meta tag
    remove_action('wp_head', 'wp_shortlink_wp_head');
}


// remove query strings
add_filter('style_loader_src', 'wp_tweaks_remove_query_string_from_scripts', 10, 2);
add_filter('script_loader_src', 'wp_tweaks_remove_query_string_from_scripts', 10, 2);
function wp_tweaks_remove_query_string_from_scripts($src)
{
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}



// desabilita emoticons wordpress
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

// desabilita XML-RPC
add_filter('xmlrpc_enabled', '__return_false');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// desabilitar self pingbacks
function disable_pingback(&$links)
{
    foreach ($links as $l => $link) {
        if (0 === strpos($link, get_option('home'))) {
            unset($links[$l]);
        }
    }
}
add_action('pre_ping', 'disable_pingback');


// desabilitar heartbeat api
add_action('init', 'stop_heartbeat', 1);
function stop_heartbeat()
{
    wp_deregister_script('heartbeat');
}

// desabilitar dashicons

function wpdocs_dequeue_dashicon()
{
    if (current_user_can('update_core')) {
        return;
    }
    wp_deregister_style('dashicons');
}
add_action('wp_enqueue_scripts', 'wpdocs_dequeue_dashicon');


// desabilita admin bar
/* Disable WordPress Admin Bar for all users but admins. */
show_admin_bar(false);


/* desabilitar notificações menos para admins */
add_action('admin_head', 'wp_tweaks_hide_update_notice', 1);
function wp_tweaks_hide_update_notice()
{
    if (!current_user_can('update_core')) {
        remove_action('admin_notices', 'update_nag', 3);
    }
}

# Remove "+ New" from admin bar
add_action('wp_before_admin_bar_render', 'wp_tweaks_remove_admin_bar_new_content', 20);
function wp_tweaks_remove_admin_bar_new_content()
{
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('new-content');
}

#Remove "Howdy" from admin bar
add_action('admin_bar_menu', 'wp_tweaks_remove_howdy', 11);
function wp_tweaks_remove_howdy($wp_admin_bar)
{
    $current_user = wp_get_current_user();
    $avatar = get_avatar($current_user->ID, 28);

    $wp_admin_bar->add_node([
    'id' => 'my-account',
    'title' => $current_user->display_name . $avatar
    ]);
}


#remove ajuda
add_filter('contextual_help_list', 'contextual_help_list_remove');
function contextual_help_list_remove()
{
    global $current_screen;
    $current_screen->remove_help_tabs();
}


#Quebra de linha no editor visual usando a tag [br]
function line_break_shortcode()
{
    return '<br />';
}
add_shortcode('br', 'line_break_shortcode');


//Limita o upload
/**
 * Filter the upload size limit for non-administrators.
 *
 * @param string $size Upload size limit (in bytes).
 * @return int (maybe) Filtered size limit.
 */
function filter_site_upload_size_limit($size)
{
    if (! current_user_can('manage_options')) {
        // 10 MB.
        $size = 1024 * 10000;
    }
    return $size;
}
add_filter('upload_size_limit', 'filter_site_upload_size_limit', 20);



/**
 * Remove password strength check.
 */
function iconic_remove_password_strength()
{
    wp_dequeue_script('wc-password-strength-meter');
}
add_action('wp_print_scripts', 'iconic_remove_password_strength', 10);