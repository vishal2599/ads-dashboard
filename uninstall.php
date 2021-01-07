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
$table1 = $wpdb->prefix . 'advertisements_dash';
$wpdb->query("DROP TABLE IF EXISTS $table1");

$table2 = $wpdb->prefix . 'adv_expert_categories';
$wpdb->query("DROP TABLE IF EXISTS $table2");