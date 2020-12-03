<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Base;

class SettingsLinks extends BaseController
{
    public function register()
    {
        add_filter("plugin_action_links_$this->plugin_name", [$this, 'settings_link']);
    }

    public function settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=advertisers_dashboard">Settings</a>';
        array_push($links, $settings_link);
        return $links;
    }
}
