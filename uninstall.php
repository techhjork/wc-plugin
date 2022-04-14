<?php

if(!defined('WP_UNINSTALL_PLUGIN')){ 
	wp_redirect( home_url() );
    exit;
};

 global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'postal_location';

// $q="DROP TABLE `$table_name`";
// $wpdb->query($q);