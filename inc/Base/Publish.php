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
                'user_id' => $user->ID,
                'status' => $_POST['status']
            ));

            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }

    public function handleUpdation()
    {
        if( isset( $_POST['new_advert_nonce'] ) && wp_verify_nonce( $_POST['new_advert_nonce'], 'new_advertisement_nonce') ) {
            $banner_id = $_POST['upload_adv_banner'];
            $in_story_id = $_POST['upload_adv_in_story'];
            $footer_id = $_POST['upload_adv_footer'];
            $sidebar_one_id = $_POST['upload_adv_sidebar_one'];
            $sidebar_two_id = $_POST['upload_adv_sidebar_two'];
            $affiliate = $_POST['affiliate_url'];
            $status = $_POST['status'];
            global $wpdb;
            $user = wp_get_current_user();

            $wpdb->update($wpdb->prefix . 'advertisements_dash', array(
                'banner_id' => $banner_id,
                'in_story_id' => $in_story_id,
                'footer_id' => $footer_id,
                'sidebar_one_id' => $sidebar_one_id,
                'sidebar_two_id' => $sidebar_two_id,
                'affiliate' => $affiliate,
                'user_id' => $user->ID,
                'status' => $status
            ),[
                'id' => (int)$_POST['advert_id']
            ] 
        );

            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }
}
