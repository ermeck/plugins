<?php /**
 * Plugin Name: Count views of the post
 * Description: This plugin calculate count of views of the post
 * Plugin URI: http://ermeck.com
 * Author: i2gun
 * Author URI: http://ermeck.com
 */

include dirname(__FILE__) . '/i2g-check.php';

register_activation_hook( __FILE__, 'i2g_create_field' );
add_filter( 'the_content', 'i2g_post_views' );
add_action( 'wp_head', 'i2g_add_view' );

function i2g_create_field() {
    global $wpdb;
    if (!i2g_check_field('i2g_views')) {
        $query = "ALTER TABLE $wpdb->posts ADD i2g_views INT NOT NULL DEFAULT '0'";
        $wpdb->query($query);
    }
    
}

function i2g_post_views($content) {
    if (is_page()) {
        return $content;
    };
    global $post;
    $views = $post->i2g_views;
    if (is_single()) {
        $views += 1;
    }
    return $content .'<b>Count of views </b>'.$views;
}

function i2g_add_view() {
    if (!is_single()) return;
    global $post, $wpdb; 
    $i2g_id = $post->ID;
    $views = $post->i2g_views + 1;
    $wpdb->update(
        $wpdb->posts,
        array('i2g_views' => $views),
        array('ID' => $i2g_id)
    );
}