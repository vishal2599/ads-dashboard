<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Pages;

use \AdvDashboard\Base\BaseController;

class Admin extends BaseController
{
    public function register()
    {
        add_action('admin_menu', [$this, 'add_admin_pages']);
    }
    public function add_admin_pages()
    {
        $user = wp_get_current_user();
        if (in_array('advertiser', (array) $user->roles) || in_array('administrator', (array) $user->roles)) :
            add_menu_page('Advertisers Dashboard', 'Ads Dashboard', 'read', 'advertisers_dashboard', [$this, 'admin_index'], 'dashicons-store', 110);
        endif;
    }

    public function admin_index()
    {
        global $wpdb;
        $user = wp_get_current_user();
        if (in_array('advertiser', (array) $user->roles)) :
            $sql = 'SELECT ' . $wpdb->prefix . 'advertisements_dash.*, ' . $wpdb->prefix . 'users.user_nicename FROM ' . $wpdb->prefix . 'advertisements_dash INNER JOIN ' . $wpdb->prefix . 'users ON ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $wpdb->prefix . 'users.ID WHERE ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $user->ID;

        elseif (in_array('administrator', (array) $user->roles)) :
            $sql = 'SELECT ' . $wpdb->prefix . 'advertisements_dash.*, ' . $wpdb->prefix . 'users.user_nicename FROM ' . $wpdb->prefix . 'advertisements_dash INNER JOIN ' . $wpdb->prefix . 'users ON ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $wpdb->prefix . 'users.ID';
        endif;
        $result = $wpdb->get_results($sql);

        // if (isset($_GET['ad_type']) && ($_GET['ad_type'] == 'new' || $_GET['ad_type'] == 'edit')) :
            if (in_array('advertiser', (array) $user->roles)) :
                require_once $this->plugin_path . 'templates/createAd.php';
            else :
                require_once $this->plugin_path . 'templates/advDashboard.php';
            endif;
        // endif;
    }
}
