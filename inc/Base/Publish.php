<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Base;

class Publish extends BaseController
{
    public function register()
    {
        add_action('admin_post_new_advert', [$this, 'handleAddition']);
        add_action('admin_post_edit_advert', [$this, 'handleUpdation']);
        add_action('admin_post_admin_edit_advert', [$this, 'handleAdvertisementPermissions']);
        add_filter( 'ajax_query_attachments_args', [$this, 'wpb_show_current_user_attachments'] );
    }

    public function handleAddition()
    {
        if( isset( $_POST['new_advert_nonce'] ) && wp_verify_nonce( $_POST['new_advert_nonce'], 'new_advertisement_nonce') ) {
            global $wpdb;
            $user = wp_get_current_user();

            $wpdb->insert($wpdb->prefix . 'advertisements_dash', array(
                'banner_id' => $_POST['upload_adv_banner'],
                'in_story_id' => $_POST['upload_adv_in_story'],
                'footer_id' => $_POST['upload_adv_footer'],
                'sidebar_one_id' => $_POST['upload_adv_sidebar_one'],
                'sidebar_two_id' => $_POST['upload_adv_sidebar_two'],
                'banner_url' => $_POST['banner_url'],
                'in_story_url' => $_POST['in_story_url'],
                'footer_url' => $_POST['footer_url'],
                'sidebar_one_url' => $_POST['sidebar_one_url'],
                'sidebar_two_url' => $_POST['sidebar_two_url'],
                'user_id' => $user->ID
            ));

            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }

    public function handleUpdation()
    {
        if( isset( $_POST['new_advert_nonce'] ) && wp_verify_nonce( $_POST['new_advert_nonce'], 'new_advertisement_nonce') ) {
            global $wpdb;

            $wpdb->update($wpdb->prefix . 'advertisements_dash', array(
                'banner_id' => $_POST['upload_adv_banner'],
                'in_story_id' => $_POST['upload_adv_in_story'],
                'footer_id' => $_POST['upload_adv_footer'],
                'sidebar_one_id' => $_POST['upload_adv_sidebar_one'],
                'sidebar_two_id' => $_POST['upload_adv_sidebar_two'],
                'banner_url' => $_POST['banner_url'],
                'in_story_url' => $_POST['in_story_url'],
                'footer_url' => $_POST['footer_url'],
                'sidebar_one_url' => $_POST['sidebar_one_url'],
                'sidebar_two_url' => $_POST['sidebar_two_url']
            ),[
                'user_id' => (int)$_POST['advertiser_id']
            ] 
        );

            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }

    public function handleAdvertisementPermissions()
    {
        if( isset( $_POST['edit_advertisement_data_nonce'] ) && wp_verify_nonce( $_POST['edit_advertisement_data_nonce'], 'edit_advertisement_data_nonce') ) {
            global $wpdb;

            $wpdb->update($wpdb->prefix . 'advertisements_dash', array(
                'membership_type' => $_POST['membership_type'],
                'status' => $_POST['status']
            ),[
                'id' => (int)$_POST['advert_id']
            ] 
        );

            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }
    function wpb_show_current_user_attachments($query)
    {
        $user_id = get_current_user_id();
        if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts') ) {
            $query['author'] = $user_id;
        }
        return $query;
    }
}
