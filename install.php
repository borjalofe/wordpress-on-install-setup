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


    /**
     * CATEGORIES' SETUP
     */

    /**
     * Setup custom first category.
     */
    $cat_id = 1;
    $cat_name = __('General');
    $cat_slug = sanitize_title(_x('general', 'Slug de la categoría por defecto'));

    update_option('default_category', $cat_id);

    $wpdb->insert(
        $wpdb->terms,
        array(
            'term_id'    => $cat_id,
            'name'       => $cat_name,
            'slug'       => $cat_slug,
            'term_group' => 0,
        )
    );
    $wpdb->insert(
        $wpdb->term_taxonomy,
        array(
            'term_id'     => $cat_id,
            'taxonomy'    => 'category',
            'description' => '',
            'parent'      => 0,
            'count'       => 1,
        )
    );

    /**
     * Setup other categories.
     */


    /**
     * PAGES' SETUP
     */


    /**
     * Setup home page
     */

    $content_id = 1;
    $now             = current_time('mysql');
    $now_gmt         = current_time('mysql', 1);

    $homepage_content = "<!-- wp:paragraph -->\n<p>";
    $homepage_content .= __("This is an example page. It's different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:");
    $homepage_content .= "</p>\n<!-- /wp:paragraph -->\n\n";

    $homepage_content .= "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>";
    $homepage_content .= __("Hi there! I'm a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like pi&#241;a coladas. (And gettin' caught in the rain.)");
    $homepage_content .= "</p></blockquote>\n<!-- /wp:quote -->\n\n";

    $homepage_content .= "<!-- wp:paragraph -->\n<p>";
    $homepage_content .= __('...or something like this:');
    $homepage_content .= "</p>\n<!-- /wp:paragraph -->\n\n";

    $homepage_content .= "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>";
    $homepage_content .= __('The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.');
    $homepage_content .= "</p></blockquote>\n<!-- /wp:quote -->\n\n";

    $homepage_content .= "<!-- wp:paragraph -->\n<p>";
    $homepage_content .= sprintf(
        __('As a new WordPress user, you should go to <a href="%s">your dashboard</a> to delete this page and create new pages for your content. Have fun!'),
        admin_url()
    );
    $homepage_content .= "</p>\n<!-- /wp:paragraph -->";

    $homepage_guid = get_option('home') . '/?page_id=' . $content_id;

    $wpdb->insert(
        $wpdb->posts,
        array(
            'post_author'           => $user_id,
            'post_date'             => $now,
            'post_date_gmt'         => $now_gmt,
            'post_content'          => $homepage_content,
            'post_excerpt'          => '',
            'comment_status'        => 'closed',
            'post_title'            => __('Home Page'),
            'post_name'             => __('homepage'),
            'post_modified'         => $now,
            'post_modified_gmt'     => $now_gmt,
            'guid'                  => $homepage_guid,
            'post_type'             => 'page',
            'to_ping'               => '',
            'pinged'                => '',
            'post_content_filtered' => '',
        )
    );
    $homepage_id = $wpdb->insert_id;

    $wpdb->insert(
        $wpdb->postmeta,
        array(
            'post_id'    => $content_id,
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'default',
        )
    );

    $content_id++;

    update_option('show_on_front', 'page');
    update_option('page_on_front', $homepage_id);


    /**
     * Setup blog page
     */

    $blogpage_guid = get_option('home') . '/?page_id=' . $content_id;

    $wpdb->insert(
        $wpdb->posts,
        array(
            'post_author'           => $user_id,
            'post_date'             => $now,
            'post_date_gmt'         => $now_gmt,
            'post_content'          => '',
            'post_excerpt'          => '',
            'comment_status'        => 'closed',
            'post_title'            => __('Blog Page'),
            'post_name'             => __('blog'),
            'post_modified'         => $now,
            'post_modified_gmt'     => $now_gmt,
            'guid'                  => $blogpage_guid,
            'post_type'             => 'page',
            'to_ping'               => '',
            'pinged'                => '',
            'post_content_filtered' => '',
        )
    );
    $blogpage_id = $wpdb->insert_id;

    $wpdb->insert(
        $wpdb->postmeta,
        array(
            'post_id'    => $content_id,
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'default',
        )
    );

    update_option('page_for_posts', $blogpage_id);


    /**
     * Setup privacy policy page
     */

    if (file_exists(WP_CONTENT_DIR . '/privacy.txt')) {
        $privacy_policy_content =  file_get_contents('privacy.txt', true);
    } else {
        if (!class_exists('WP_Privacy_Policy_Content')) {
            include_once(ABSPATH . 'wp-admin/includes/misc.php');
        }

        $privacy_policy_content = WP_Privacy_Policy_Content::get_default_content();
    }

    if (!empty($privacy_policy_content)) {
        $privacy_policy_guid = get_option('home') . '/?page_id=' . $content_id;

        $wpdb->insert(
            $wpdb->posts,
            array(
                'post_author'           => $user_id,
                'post_date'             => $now,
                'post_date_gmt'         => $now_gmt,
                'post_content'          => $privacy_policy_content,
                'post_excerpt'          => '',
                'comment_status'        => 'closed',
                'post_title'            => __('Privacy Policy'),
                'post_name'             => __('privacy-policy'),
                'post_modified'         => $now,
                'post_modified_gmt'     => $now_gmt,
                'guid'                  => $privacy_policy_guid,
                'post_type'             => 'page',
                'post_status'           => 'draft',
                'to_ping'               => '',
                'pinged'                => '',
                'post_content_filtered' => '',
            )
        );
        $wpdb->insert(
            $wpdb->postmeta,
            array(
                'post_id'    => $content_id,
                'meta_key'   => '_wp_page_template',
                'meta_value' => 'default',
            )
        );
        update_option('wp_page_for_privacy_policy', $content_id);
    }

    $content_id++;


    /**
     * Setup cookies policy page
     * 
     * @ToDo
     */


    /**
     * Setup utc page
     * 
     * @ToDo
     */


    /**
     * Setup about page
     * 
     * @ToDo
     */


    /**
     * Setup contact page
     * 
     * @ToDo
     */


    /**
     * MENUS' SETUP
     * 
     * @ToDo
     */


    /**
     * OPTIONS' SETUP
     */

    update_user_meta($user_id, 'show_welcome_panel', 1);

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
