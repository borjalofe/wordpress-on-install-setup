<?php

/**
 * wp_install_defaults creates the first content for a newly installed
 * WordPress site.
 *
 * @since 2.1.0
 *
 * @global wpdb       $wpdb
 * @global WP_Rewrite $wp_rewrite
 * @global string     $table_prefix
 *
 * @param int $user_id User ID.
 */
function wp_install_defaults($user_id)
{
    global $wpdb, $wp_rewrite, $table_prefix;

    $cat_id = 1;
    $content_id = 1;
    $mainMenu = [
        'id' => wp_get_nav_menu_object('primary')['term_id'],
        'pages' => []
    ];
    $footerMenu = [
        'id' => wp_get_nav_menu_object('footer')['term_id'],
        'pages' => []
    ];

    /**
     * PLUGINS' SETUP
     */

    $plugins = [
        'contact-form-7/wp-contact-form-7.php',
        'duplicate-post/duplicate-post.php',
        'editorplus/editorplus.php',
        'ewww-image-optimizer/ewww-image-optimizer.php',
        'machete/machete.php',
        'query-monitor/query-monitor.php',
        'subscribe-to-comments-reloaded/subscribe-to-comments-reloaded.php',
        'wordpress-importer/wordpress-importer.php',
        'wp-activity-log/wp-activity-log.php',
        'wordpress-seo/wp-seo.php'
    ];

    foreach ($plugins as $plugin) {
        add_plugin($plugin);
    }

    /**
     * CATEGORIES' SETUP
     */
    // Default category
    create_category(
        $cat_id++,
        'General',
        'Artículos sin una categoría concreta',
        'general',
        true
    );


    /**
     * PAGES' SETUP
     */
    // Substitutions
    $user_data = get_userdata($user_id);
    $substitutions = [
        'URL' => get_option('siteurl'),
        'Marca' => get_option('blogname'),
        'Correo' => $user_data->user_email
    ];

    // Homepage
    $mainMenu['pages'][] = create_page(
        $user_id,
        $content_id++,
        'Inicio',
        '',
        'home',
        '',
        $substitutions
    );

    // About Page
    $mainMenu['pages'][] = create_page(
        $user_id,
        $content_id++,
        'Sobre Mí',
        '',
        'page',
        '',
        $substitutions
    );

    // Blogpage
    $mainMenu['pages'][] = create_page(
        $user_id,
        $content_id++,
        'Blog',
        '',
        'blog',
        '',
        $substitutions
    );

    // Contact Page
    $mainMenu['pages'][] = create_page(
        $user_id,
        $content_id++,
        'Contacto',
        '',
        'page',
        '',
        $substitutions
    );

    // Privacy Policy Page
    $footerMenu['pages'][] = create_page(
        $user_id,
        $content_id++,
        'Política de Privacidad',
        '',
        'privacy',
        '',
        $substitutions
    );

    // Cookies Policy Page
    $footerMenu['pages'][] = create_page(
        $user_id,
        $content_id++,
        'Política de Cookies',
        '',
        'cookies',
        '',
        $substitutions
    );



    /**
     * MENUS' SETUP
     * 
     * @ToDo
     */


    /**
     * OPTIONS' SETUP
     */

    update_user_meta($user_id, 'show_welcome_panel', 1);

    update_option('blogdescription', '');

    update_option('selection', 'custom');


    /**
     * Setup permalink structure and update permalinks
     */
    update_option('permalink_structure', '/%postname%/');
    $wp_rewrite->init();
    $wp_rewrite->flush_rules();



    /**
     * Setup date&time setup
     */
    update_option('date_format', 'd/m/Y');
    update_option('links_updated_date_format', 'd/m/Y H:i');
    update_option('time_format', 'H:i');


    /**
     * Setup the start of the week to Monday
     */
    update_option('start_of_week', 1);


    /**
     * Setup timezone
     */
    update_option('timezone_string', 'Europe/Madrid');


    /**
     * Disable the year/month folder structure inside the uploads folder
     */
    update_option('uploads_use_yearmonth_folders', 0);


    /**
     * Disable smilies
     */
    update_option('use_smilies', 0);


    /**
     * Setup language
     */
    update_option('WPLANG', 'es_ES');
}

/**
 * create_page creates a new page.
 *
 * @global Object $wpdb
 *
 * @param int $user_id      User ID.
 * @param int $id           Page ID.
 * @param string $title     Page title.
 * @param string $content   Page content. Defaults to empty.
 * @param string $type      Post type. Defaults to 'post'.
 * @param string $slug      Page slug. Defaults to empty.
 * @param int $now          Creation time. Defaults to 0.
 * @param int $now_gmt      Creation time (GMT format). Defaults to 0.
 * 
 * @return int              New page's ID.
 */
function create_page(
    int $user_id,
    int $id,
    string $title,
    string $content = '',
    string $type = 'post',
    string $slug = '',
    $substitutions = [],
    int $now = 0,
    int $now_gmt = 0
) {
    global $wpdb;

    if ($now === 0) {
        $now = current_time('mysql');
    }

    if ($now_gmt === 0) {
        $now_gmt = current_time('mysql', 1);
    }

    if (empty($content)) {
        if (file_exists(WP_CONTENT_DIR . '/uploads/pages/' . $type . '.txt')) {
            $content =  file_get_contents(WP_CONTENT_DIR . '/uploads/pages/' . $type . '.txt', true);
        } elseif ($type === 'privacy') {

            if (!class_exists('WP_Privacy_Policy_Content')) {
                include_once(ABSPATH . 'wp-admin/includes/misc.php');
            }

            $content = WP_Privacy_Policy_Content::get_default_content();
        }
    }

    if (
        !empty($content)
        && count($substitutions) > 0
    ) {
        foreach ($substitutions as $search => $replace) {
            $content = str_replace('{{' . $search . '}}', $replace, $content);
        }
    }

    if (empty($slug)) {
        $slug = get_slug_from_name($title);
    }

    $pageType = $type;

    if (
        $type !== 'post'
        && $type !== 'page'
    ) {
        $pageType =  'page';
    }

    $guid = get_option('home') . '/?page_id=' . $id;

    $wpdb->insert(
        $wpdb->posts,
        array(
            'post_author'           => $user_id,
            'post_date'             => $now,
            'post_date_gmt'         => $now_gmt,
            'post_content'          => $content,
            'post_excerpt'          => '',
            'comment_status'        => 'closed',
            'post_title'            => $title,
            'post_name'             => $slug,
            'post_modified'         => $now,
            'post_modified_gmt'     => $now_gmt,
            'guid'                  => $guid,
            'post_type'             => $pageType,
            'to_ping'               => '',
            'pinged'                => '',
            'post_content_filtered' => '',
        )
    );
    $newId = $wpdb->insert_id;

    $wpdb->insert(
        $wpdb->postmeta,
        array(
            'post_id'    => $id,
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'default',
        )
    );

    if ($type === 'home') {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $newId);
    } elseif ($type === 'blog') {
        update_option('page_for_posts', $newId);
    } elseif ($type === 'privacy') {
        update_option('wp_page_for_privacy_policy', $newId);
    }

    return $id;
}


/**
 * config_plugin configs a plugin.
 *
 * @param string $plugin    Plugin name.
 * 
 * @return bool             Whether the plugin has been set or not.
 */
function config_page($page)
{
    $config_file = WP_CONTENT_DIR . "/uploads/pages/$page.json";
    if (!file_exists($config_file)) {
        return false;
    }

    $config = json_decode(file_get_contents($config_file, true), true);
    foreach ($config as $option_key => $option_value) {
        update_option($option_key, $option_value);
    }

    return true;
}


/**
 * create_category creates a new category.
 *
 * @global Object $wpdb
 *
 * @param int $id               Category ID.
 * @param string $name          Category name.
 * @param string $description   Category description. Defaults to ''.
 * @param string $slug          Category slug. Defaults to empty.
 * @param bool $is_default      To set the new category as the default one.
 *                              Defaults to false.
 * 
 * @return int                  New category's ID.
 */
function create_category(
    int $id,
    string $name,
    string $description = '',
    string $slug = '',
    bool $isDefault = false
) {
    global $wpdb;

    if (empty($slug)) {
        $slug = get_slug_from_name($name);
    }
    $slug = sanitize_title(_x($slug, $slug));

    if ($isDefault) {
        update_option('default_category', $id);
    }

    $wpdb->insert(
        $wpdb->terms,
        array(
            'term_id'    => $id,
            'name'       => $name,
            'slug'       => $slug,
            'term_group' => 0,
        )
    );
    $wpdb->insert(
        $wpdb->term_taxonomy,
        array(
            'term_id'     => $id,
            'taxonomy'    => 'category',
            'description' => $description,
            'parent'      => 0,
            'count'       => 1,
        )
    );

    return $id;
}


/**
 * add_plugin adds a plugin by its slug.
 *
 * @param string $plugin_slug   Plugin slug.
 * 
 * @return bool                 Whether the plugin has been activated or not.
 */
function add_plugin(string $plugin_slug)
{
    // How do I get the plugin?
    $current = get_option('active_plugins');
    $plugin  = plugin_basename(trim($plugin_slug));

    if (
        !in_array($plugin, $current)
        && replace_plugin($plugin_slug)
    ) {
        return true;
    }

    return false;
}


/**
 * replace_plugin adds or replace a plugin by its slug.
 *
 * @param string $plugin    Plugin name.
 * 
 * @return bool             Whether the plugin has been activated or not.
 */
function replace_plugin($plugin)
{
    $plugin_name = explode('/', trim($plugin))[0];
    $plugin_zip = "https://downloads.wordpress.org/plugin/$plugin_name.latest-stable.zip";


    if (is_plugin_installed($plugin)) {
        upgrade_plugin($plugin);
        $installed = true;
    } else {
        $installed = install_plugin($plugin_zip);
        config_plugin($plugin_name);
    }

    if (!is_wp_error($installed) && $installed) {

        $activate = activate_plugin($plugin);

        //if (is_null($activate)) {
        //    deactivate_plugins(array($plugin));
        //}

        return true;
    }
    return false;
}


/**
 * config_plugin configs a plugin.
 *
 * @param string $plugin    Plugin name.
 * 
 * @return bool             Whether the plugin has been set or not.
 */
function config_plugin($plugin)
{
    $config_file = WP_CONTENT_DIR . "/uploads/plugins/$plugin.json";
    if (!file_exists($config_file)) {
        return false;
    }

    $config = json_decode(file_get_contents($config_file, true), true);
    foreach ($config as $option_key => $option_value) {
        update_option($option_key, $option_value);
    }

    return true;
}


/**
 * is_plugin_installed checks if a plugin is installed.
 *
 * @param string $slug      Plugin slug.
 * 
 * @return bool             Whether the plugin is installed or not.
 */
function is_plugin_installed($slug)
{
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $all_plugins = get_plugins();

    if (!empty($all_plugins[$slug])) {
        return true;
    } else {
        return false;
    }
}


/**
 * install_plugin installs a plugin from a zip file.
 *
 * @param string $slug      Plugin's zip file path or url.
 * 
 * @return bool|string      True if the plugin is installed correctly or error
 *                          message otherwise.
 */
function install_plugin($plugin_zip)
{
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    wp_cache_flush();

    $upgrader = new Plugin_Upgrader();
    $installed = $upgrader->install($plugin_zip);

    return $installed;
}

function upgrade_plugin($plugin_slug)
{
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    wp_cache_flush();

    $upgrader = new Plugin_Upgrader();
    $upgraded = $upgrader->upgrade($plugin_slug);

    return $upgraded;
}


/**
 * get_slug_from_name gets the correct slug from an intem name.
 *
 * @global Object $wpdb
 *
 * @param string $name          Item name.
 * 
 * @return int                  Item slug.
 */
function get_slug_from_name(string $name)
{
    return strtolower(
        str_replace(
            ' ',
            '-',
            str_replace(
                '.',
                '',
                str_replace(
                    '&',
                    '',
                    str_replace(
                        'acute;',
                        '',
                        str_replace(
                            'tilde;',
                            '',
                            htmlentities($name)
                        )
                    )
                )
            )
        )
    );
}
