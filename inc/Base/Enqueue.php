<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Base;

class Enqueue extends BaseController
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueue'));
        add_action('wp_enqueue_scripts', array($this, 'frontendEnqueue'));
    }

    public function adminEnqueue()
    {
        wp_enqueue_style('alecaddd-style', $this->plugin_url . 'assets/admin/admin.css');
        wp_enqueue_script('alecaddd-script', $this->plugin_url . 'assets/admin/admin.js');
        wp_enqueue_media();
        wp_enqueue_style('dataTable-style', '//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css');
        wp_enqueue_script('dataTable-js', '//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js');
    }

    public function frontendEnqueue()
    {
        wp_enqueue_script('advfrontend-js', $this->plugin_url . 'assets/frontend/frontend.js', ['jquery']);
        wp_enqueue_style('advfrontend-css', $this->plugin_url . 'assets/frontend/frontend.css');
        wp_localize_script('advfrontend-js', 'advAjax', ['url' => 'http://threefortyb.wpengine.com/wp-admin/admin-ajax.php', 'nonce' => wp_create_nonce('advDashboardCreate')]);
    }
}
