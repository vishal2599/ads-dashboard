<?php

/**
 * 
 * Trigger this file on Plugin Uninstall
 * 
 * @package AlecadddPlugin
 * 
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Clear data stored in database

global $wpdb;
$table_name = $wpdb->prefix . 'advertisements_dash';
$wpdb->query("DROP TABLE IF EXISTS $table_name");