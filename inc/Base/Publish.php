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
        add_action('admin_post_create_expCat', [$this, 'createExpCategory']);
        add_action('admin_post_delete_expCat', [$this, 'deleteExpCategory']);
    }

    public function handleAddition()
    {
        if (isset($_POST['new_advert_nonce']) && wp_verify_nonce($_POST['new_advert_nonce'], 'new_advertisement_nonce')) {
            global $wpdb;
            // $user = wp_get_current_user();
            $user_id = $_POST['advertiser_id'];

            $ad_data = [
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
            ];
            $categories = $_POST['company_category'];
            $cats = [];
            foreach( $categories as $cat ){
                $cats[] = $cat;
            }

            $company_data = [
                'company_logo' => $_POST['company_logo'],
                'company_name' => $_POST['company_name'],
                'company_category' =>  $cats,
                'company_url' => $_POST['company_url'],
                'company_description' => $_POST['company_description']
            ];
            $event_data = [];
            $event_date = $_POST['event_date'];
            $event_title = $_POST['event_title'];
            $event_description = $_POST['event_description'];
            
            for ($i=0; $i < count($event_date); $i++) {
                $event_data[$i]['event_date'] = $event_date[$i];
                $event_data[$i]['event_title'] = $event_title[$i];
                $event_data[$i]['event_description'] = $event_description[$i];
            }
            $sql = 'SELECT user_id FROM ' . $wpdb->prefix . 'advertisements_dash WHERE user_id=' . $user_id;
            $ids = $wpdb->get_row($sql);
            if (!empty($ids)) {
                $wpdb->update(
                    $wpdb->prefix . 'advertisements_dash',
                    [
                        'ad_data' => json_encode($ad_data),
                        'company_data' =>  json_encode($company_data),
                        'event_data' => json_encode($event_data)
                    ],
                    [
                        'user_id' => $user_id
                    ]
                );
            } else {
                $wpdb->insert($wpdb->prefix . 'advertisements_dash', array(
                    'ad_data' => json_encode($ad_data),
                    'user_id' => $user_id,
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
            // $user = wp_get_current_user();
            $user_id = $_POST['advertiser_id'];
            // echo '<pre>';
            // print_r($_POST); die;
            $ad_data = [
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
            ];
            $categories = $_POST['company_category'];
            $cats = [];
            foreach( $categories as $cat ){
                $cats[] = $cat;
            }

            $company_data = [
                'company_logo' => $_POST['company_logo'],
                'company_name' => sanitize_text_field( $_POST['company_name'] ),
                'company_category' => $cats,
                'company_url' => $_POST['company_url'],
                'company_description' => $_POST['company_description']
            ];
            $event_data = [];
            $event_date = $_POST['event_date'];
            $event_title = $_POST['event_title'];
            $event_description = $_POST['event_description'];
            
            for ($i=0; $i < count($event_date); $i++) {
                $event_data[$i]['event_date'] = $event_date[$i];
                $event_data[$i]['event_title'] = $event_title[$i];
                $event_data[$i]['event_description'] = $event_description[$i];
            }
            
            $wpdb->update(
                $wpdb->prefix . 'advertisements_dash',
                [
                    'ad_data' => json_encode($ad_data),
                    'company_data' =>  json_encode($company_data),
                    'event_data' => json_encode($event_data)
                ],
                [
                    'user_id' => $user_id
                ]
            );

            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }

    public function handleAdvertisementPermissions()
    {
        if (isset($_POST['edit_advertisement_data_nonce']) && wp_verify_nonce($_POST['edit_advertisement_data_nonce'], 'edit_advertisement_data_nonce')) {
            global $wpdb;
            $user = wp_get_current_user();

            $ad_id = (int)$_POST['advert_id'];
            $sql = 'SELECT id FROM ' . $wpdb->prefix . 'advertisements_dash WHERE id=' . $ad_id;
            $res = $wpdb->get_row($sql);
            if (!empty($res)) {
                $wpdb->update($wpdb->prefix . 'advertisements_dash', array(
                    'membership_type' => $_POST['membership_type'],
                    'status' => $_POST['status']
                ), [
                    'id' => (int)$_POST['advert_id']
                ]);
            } 

            wp_redirect('/wp-admin/admin.php?page=advertisers_dashboard');
        }
    }

    public function createExpCategory()
    {
        global $wpdb;
        if (isset($_POST['expert_category_create_nonce']) && wp_verify_nonce($_POST['expert_category_create_nonce'], 'expert_category_create_nonce')):
            $exp_category = $_POST['exp_category'];
            $wpdb->insert($wpdb->prefix . 'adv_expert_categories', array(
                'category_name' => $exp_category
            ));

            wp_redirect('/wp-admin/admin.php?page=adv_experts_directory');
        endif;
    }

    public function deleteExpCategory()
    {
        global $wpdb;
        if (isset($_POST['expert_category_delete_nonce']) && wp_verify_nonce($_POST['expert_category_delete_nonce'], 'expert_category_delete_nonce')):
            $id = (int) $_POST['exp_category_id'];
            $wpdb->delete($wpdb->prefix . 'adv_expert_categories', array(
                'id' => $id
            ));

            wp_redirect('/wp-admin/admin.php?page=adv_experts_directory');
        endif;
    }
}
