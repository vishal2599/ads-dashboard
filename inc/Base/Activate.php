<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Base;

class Activate
{
    public static function activate()
    {
        $thisClass = new Activate();
        $thisClass->addAdvertiser();
        $thisClass->createTable();
        flush_rewrite_rules();
    }

    public function addAdvertiser()
    {
        add_role('advertiser', 'Advertiser', [
            'read' => true,
            'upload_files' => true
        ]);
    }

    public function createTable()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'advertisements_dash';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name(id int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT, user_id int(10) NOT NULL, banner_id int(10), banner_url text, in_story_id int(10), in_story_url text, footer_id int(10), footer_url text, sidebar_one_id int(10), sidebar_one_url text, sidebar_two_id int(10),sidebar_two_url text, membership_type varchar(255), status BOOL DEFAULT 0);";
        $wpdb->query($sql);
    }
}
