<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
global $wpdb;
$table_name = $wpdb->prefix . "wpcivimrf_profile";
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . "wpcmrf_core_call";
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
delete_option("wpcmrf_version");