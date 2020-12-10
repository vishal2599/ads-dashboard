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
        $sql = "CREATE TABLE IF NOT EXISTS $table_name(
            `id` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            `user_id` int(10) NOT NULL,
            `ad_data` longtext DEFAULT NULL CHECK (json_valid(`ad_data`)),
            `company_data` longtext DEFAULT NULL CHECK (json_valid(`company_data`)),
            `event_data` longtext DEFAULT NULL CHECK (json_valid(`event_data`)),
            `membership_type` varchar(255) DEFAULT NULL,
            `status` tinyint(1) DEFAULT 0
          );";
        $wpdb->query($sql);
    }
}
