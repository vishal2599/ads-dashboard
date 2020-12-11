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
    }

    public function handleAddition()
    {
        if (isset($_POST['new_advert_nonce']) && wp_verify_nonce($_POST['new_advert_nonce'], 'new_advertisement_nonce')) {
            global $wpdb;
            $user = wp_get_current_user();
            
            $ad_data = [
                'banner_id' => sanitize_text_field($_POST['upload_adv_banner']),
                'in_story_id' => sanitize_text_field($_POST['upload_adv_in_story']),
                'footer_id' => sanitize_text_field($_POST['upload_adv_footer']),
                'sidebar_one_id' => sanitize_text_field($_POST['upload_adv_sidebar_one']),
                'sidebar_two_id' => sanitize_text_field($_POST['upload_adv_sidebar_two']),
                'banner_url' => sanitize_text_field($_POST['banner_url']),
                'in_story_url' => sanitize_text_field($_POST['in_story_url']),
                'footer_url' => sanitize_text_field($_POST['footer_url']),
                'sidebar_one_url' => sanitize_text_field($_POST['sidebar_one_url']),
                'sidebar_two_url' => sanitize_text_field($_POST['sidebar_two_url']),
            ];

            $company_data = [
                'company_name' => sanitize_text_field($_POST['company_name']),
                'company_url' => sanitize_text_field($_POST['company_url']),
                'company_description' => sanitize_text_field($_POST['company_description'])
            ];
            $event_data = [];
            $event_date = $_POST['event_date']; $i = 0;
            foreach($event_date as $ev){
                $event_data[$i]['event_date'] = $ev;
                $i++;
            }
            $event_title = $_POST['event_title']; $j = 0;
            foreach($event_title as $et){
                $event_data[$j]['event_title'] = $et;
                $j++;
            }
            $event_description = $_POST['event_description']; $k = 0;
            foreach($event_description as $ed){
                $event_data[$k]['event_description'] = $ed;
                $k++;
            }
            $sql = 'SELECT user_id FROM ' . $wpdb->prefix . 'advertisements_dash WHERE user_id='.$user->ID;
            $ids = $wpdb->get_row($sql);
            if( !empty($ids) ){
                $wpdb->update($wpdb->prefix . 'advertisements_dash', [
                    'ad_data' => json_encode($ad_data),
                    'company_data' =>  json_encode($company_data),
                    'event_data' => json_encode($event_data)
                ],
                [
                    'user_id' => $user->ID
                ]);
            } else {
                $wpdb->insert($wpdb->prefix . 'advertisements_dash', array(
                    'ad_data' => json_encode($ad_data),
                    'user_id' => $user->ID,
                    'company_data' =>  json_encode($company_data),
                    'event_data' => json_encode($event_data)
                ));
            }


            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }

    public function handleUpdation()
    {
        if (isset($_POST['new_advert_nonce']) && wp_verify_nonce($_POST['new_advert_nonce'], 'new_advertisement_nonce')) {

            global $wpdb;
            $user = wp_get_current_user();
            $ad_data = [
                'banner_id' => sanitize_text_field($_POST['upload_adv_banner']),
                'in_story_id' => sanitize_text_field($_POST['upload_adv_in_story']),
                'footer_id' => sanitize_text_field($_POST['upload_adv_footer']),
                'sidebar_one_id' => sanitize_text_field($_POST['upload_adv_sidebar_one']),
                'sidebar_two_id' => sanitize_text_field($_POST['upload_adv_sidebar_two']),
                'banner_url' => sanitize_text_field($_POST['banner_url']),
                'in_story_url' => sanitize_text_field($_POST['in_story_url']),
                'footer_url' => sanitize_text_field($_POST['footer_url']),
                'sidebar_one_url' => sanitize_text_field($_POST['sidebar_one_url']),
                'sidebar_two_url' => sanitize_text_field($_POST['sidebar_two_url']),
            ];

            $company_data = [
                'company_name' => sanitize_text_field($_POST['company_name']),
                'company_url' => sanitize_text_field($_POST['company_url']),
                'company_description' => sanitize_text_field($_POST['company_description'])
            ];
            $event_data = [];
            $event_date = $_POST['event_date']; $i = 0;
            foreach($event_date as $ev){
                $event_data[$i]['event_date'] = $ev;
                $i++;
            }
            $event_title = $_POST['event_title']; $j = 0;
            foreach($event_title as $et){
                $event_data[$j]['event_title'] = $et;
                $j++;
            }
            $event_description = $_POST['event_description']; $k = 0;
            foreach($event_description as $ed){
                $event_data[$k]['event_description'] = $ed;
                $k++;
            }
            $wpdb->update($wpdb->prefix . 'advertisements_dash', [
                'ad_data' => json_encode($ad_data),
                'company_data' =>  json_encode($company_data),
                'event_data' => json_encode($event_data)
            ],
            [
                'user_id' => $user->ID
            ]);

            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }

    public function handleAdvertisementPermissions()
    {
        if (isset($_POST['edit_advertisement_data_nonce']) && wp_verify_nonce($_POST['edit_advertisement_data_nonce'], 'edit_advertisement_data_nonce')) {
            global $wpdb;
            $user = wp_get_current_user();
            
            $ad_id = (int)$_POST['advert_id'];
            $sql = 'SELECT id FROM ' . $wpdb->prefix . 'advertisements_dash WHERE id='.$ad_id;
            $res = $wpdb->get_row($sql);
            if( !empty($res) ){
            $wpdb->update($wpdb->prefix . 'advertisements_dash', array(
                'membership_type' => $_POST['membership_type'],
                'status' => $_POST['status']
            ), [
                'id' => (int)$_POST['advert_id']
            ]);
            } else {
                $wpdb->insert($wpdb->prefix . 'advertisements_dash', array(
                    'membership_type' => $_POST['membership_type'],
                    'status' => $_POST['status'],
                    'user_id' =>  $user->ID
                ));
            }

            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }
}
