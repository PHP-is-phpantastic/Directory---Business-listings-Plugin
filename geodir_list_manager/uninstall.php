<?php
/**
 * Uninstall GeoDirectory List Manager
 *
 * Uninstalling BuddyPress Compliments deletes the plugin data & settings.
 *
 * @package GD_Lists
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

$geodir_settings = get_option( 'geodir_settings' );

if ( ( ! empty( $geodir_settings ) && ( ! empty( $geodir_settings['admin_uninstall'] ) || ! empty( $geodir_settings['uninstall_geodir_lists'] ) ) ) || ( defined( 'GEODIR_UNINSTALL_GEODIR_LISTS' ) && true === GEODIR_UNINSTALL_GEODIR_LISTS ) ) {
    global $wpdb;

    wp_delete_post( geodir_get_option( 'geodir_add_list_page' ), true );

    $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type LIKE gd_list';" );

    wp_cache_flush();
}