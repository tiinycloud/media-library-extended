<?php
/**
 * Plugin Name: Media Library Extended
 * Plugin URI:  https://tiinycloud.com
 * Description: Enhances WordPress Media Library by allowing search by media filename.
 * Version:     1.0.0
 * Author:      TiinyCloud
 * Author URI:  https://tiinycloud.com
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: media-library-extended
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Extend WordPress Media Library search to include filenames.
 *
 * This function modifies the search query to look inside the _wp_attached_file meta key,
 * which stores filenames of uploaded media files.
 *
 * @param array    $clauses  The SQL clauses for the search query.
 * @param WP_Query $wp_query The query object.
 * @return array Modified query clauses.
 */
function tiinycloud_mle_extend_media_library_search($clauses, $wp_query) {
    global $wpdb;

    // Ensure this only affects Media Library searches in admin
    if (is_admin() && isset($wp_query->query['s']) && $wp_query->is_search) {
        $search_term = esc_sql($wpdb->esc_like($wp_query->query['s']));

        // Modify the WHERE clause to allow filename searching
        $clauses['where'] .= $wpdb->prepare(
            " OR $wpdb->posts.ID IN (
                SELECT post_id FROM $wpdb->postmeta 
                WHERE meta_key = '_wp_attached_file' 
                AND RIGHT(meta_value, LENGTH(meta_value) - LOCATE('/', meta_value, 4)) LIKE %s
            )",
            "%{$search_term}%"
        );
    }

    return $clauses;
}

// Apply the filter to enhance Media Library search
add_filter('posts_clauses', 'tiinycloud_mle_extend_media_library_search', 10, 2);

/**
 * Plugin activation hook.
 */
function tiinycloud_mle_activate() {
    // Placeholder for future setup tasks
}
register_activation_hook(__FILE__, 'tiinycloud_mle_activate');

/**
 * Plugin deactivation hook.
 */
function tiinycloud_mle_deactivate() {
    // Placeholder for future cleanup tasks
}
register_deactivation_hook(__FILE__, 'tiinycloud_mle_deactivate');
