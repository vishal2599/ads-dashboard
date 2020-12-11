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
}
