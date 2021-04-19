<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Base;

class User extends BaseController
{
    public function register()
    {
        add_filter('ajax_query_attachments_args', [$this, 'wpb_show_current_user_attachments']);
        add_action('update_advDashboard', [$this, 'execute_advDashboard']);
        if (!wp_next_scheduled('update_advDashboard')) {
            wp_schedule_event(time(), 'hourly', 'update_advDashboard');
        }
        add_filter('admin_body_class', [$this, 'role_admin_body_class']);
        add_action('admin_init', [$this, 'remove_dashboard_meta']);
        add_action('wp_dashboard_setup', [$this, 'add_dashboard_widget']);
    }
    public function wpb_show_current_user_attachments($query)
    {
        $user_id = get_current_user_id();
        if ($user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts')) {
            $query['author'] = $user_id;
        }
        return $query;
    }

    public function execute_advDashboard()
    {
        global $wpdb;
        $user_query = new \WP_User_Query(array('role' => 'Advertiser'));
        $result = $user_query->get_results();
        $sql = 'SELECT user_id FROM ' . $wpdb->prefix . 'advertisements_dash';
        $ids = $wpdb->get_col($sql);
        foreach ($result as $data) {
            if (!in_array($data->data->ID, $ids)) {
                $wpdb->insert($wpdb->prefix . 'advertisements_dash', ['user_id' => $data->data->ID]);
            }
        }
    }
    public function role_admin_body_class($classes)
    {
        global $current_user;
        foreach ($current_user->roles as $role)
            $classes .= ' role-' . $role;
        return trim($classes);
    }
    function remove_dashboard_meta()
    {
        $user = \wp_get_current_user();
        if (in_array('advertiser', (array) $user->roles)) :
            remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
            remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
            remove_meta_box('dashboard_primary', 'dashboard', 'side');
            remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
            remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
            remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
            remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
            remove_meta_box('dashboard_activity', 'dashboard', 'normal');
            remove_meta_box('wpe_dify_news_feed', 'dashboard', 'normal');

        endif;
    }
    public function add_dashboard_widget()
    {
        wp_add_dashboard_widget('advDashboard_widget', 'Advertisers Dasboard', [$this, 'dashboardWidgetCallback']);
    }
    public function dashboardWidgetCallback()
    {
        $user = \wp_get_current_user();
        if (in_array('advertiser', (array) $user->roles)) :
            echo "<h2>Welcome to the 340B Report Advertisers Dashboard.</h2>";
            echo '<p>To update your Ads, Company Profile, Logo, or Events, please click on "<a href="/wp-admin/admin.php?page=advertisers_dashboard">Ads Dashboard</a>" in the left-hand menu.</p>';
        endif;
    }
}
