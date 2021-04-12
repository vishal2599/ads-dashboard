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
        if (in_array('administrator', (array) $user->roles)) :
            add_submenu_page('advertisers_dashboard', 'Experts Directory', 'Experts Directory', 'read', 'adv_experts_directory', [$this, 'expertsDirectory']);
            add_submenu_page('advertisers_dashboard', 'MailChimp NewsLetter', 'MailChimp NewsLetter', 'read', '340b_mailchimp_newsletter', [$this, 'MailchimpNewsletter']);
        endif;
    }

    // public function setSubpages()
    // {
    //     $this->subpages = array(
    //         array(
    //             'parent_slug' => 'advertisers_dashboard',
    //             'page_title' => 'Expert Directories',
    //             'menu_title' => 'Expert Directories',
    //             'capability' => 'read',
    //             'menu_slug' => 'expert_directories',
    //             'callback' => array($this->callbacks, 'adminCpt')
    //         ),
    //         array(
    //             'parent_slug' => 'advertisers_dashboard',
    //             'page_title' => 'MailChimp NewsLetter',
    //             'menu_title' => 'MailChimp NewsLetter',
    //             'capability' => 'read',
    //             'menu_slug' => '340b_mailchimp_newsletter',
    //             'callback' => array($this->callbacks, 'MailchimpNewsletter')
    //         )
    //     );
    // }

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

        if (in_array('advertiser', (array) $user->roles)) :
            require_once $this->plugin_path . 'templates/createAd.php';
        elseif (in_array('administrator', (array) $user->roles)) :
            if (isset($_GET['ad_type']) && ($_GET['ad_type'] == 'new' || $_GET['ad_type'] == 'edit')) {
                require_once $this->plugin_path . 'templates/editAdvert.php';
            } elseif (isset($_GET['ad_type']) && $_GET['ad_type'] == 'edit_data') {
                require_once $this->plugin_path . 'templates/createAd.php';
            } else {
                require_once $this->plugin_path . 'templates/advDashboard.php';
            }
        endif;
    }

    public function expertsDirectory()
    {
        $user = wp_get_current_user();
        if (in_array('administrator', (array) $user->roles)) :
            global $wpdb;
            $sql = 'SELECT * FROM ' . $wpdb->prefix . 'adv_expert_categories';
            $result = $wpdb->get_results($sql);
            require_once $this->plugin_path . 'templates/expertCategories.php';
        endif;
    }

    public function mailchimpNewsletter()
    {
        require_once $this->plugin_path . 'templates/mailchimp-newsletter.php';
    }
}
