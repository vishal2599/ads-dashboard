<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Base;

class AdGenerator extends BaseController
{
    public function register()
    {
        add_action('wp_ajax_showAdverts', array($this, 'showAdverts'));
        add_action('wp_ajax_nopriv_showAdverts', array($this, 'showAdverts'));
    }

    public function showAdverts()
    {
        check_ajax_referer('advDashboardCreate', 'nonce');
        global $wpdb;
        $case = '';
        if ($_REQUEST['container'] == 'home') {
            $case = 1;
            $sql = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1 ORDER BY RAND(), FIELD(membership_type, "ultra_diamond", "diamond", "emerald") LIMIT 5;';
        } elseif ($_REQUEST['container'] == 'post') {
            if ((int)$_REQUEST['cat_id'] == 2) {
                $case = 2;
                $sql = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1 AND membership_type="ultra_diamond" ORDER BY RAND() LIMIT 5;';
            } else {
                $case = 3;
                $sql = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1 ORDER BY RAND(), FIELD(membership_type, "ultra_diamond", "diamond", "emerald") LIMIT 5;';
            }
        }
        $ids = $wpdb->get_results($sql);
        $ad_types = ['banner', 'in_story', 'footer', 'sidebar_one', 'sidebar_two'];

        $html = [];
        for ($i = 0; $i < 5; $i++) :
            switch ($i) {
                case 0:
                    $rand = rand(0, count($ids) - 1);
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->banner_id);
                    $url =  $ad_data->banner_url;
                    if ($ids[$rand]->membership_type == 'emerald' && $case == 1) {
                        $sql = 'SELECT ad_data FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1 AND ( membership_type="diamond" OR membership_type="ultra_diamond" ) ORDER BY RAND() LIMIT 1;';
                        $result = $wpdb->get_results($sql);
                        $ad_data = json_decode($result[0]->ad_data);
                        $html[$ad_types[$i]] .= '<div class="container advDashboard banner"><a target="_blank" href="'.$ad_data->banner_url.'"><img src="' . $ad_data->banner_id . '"></a></div>';
                    } else {
                        $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    }
                    break;
                case 1:
                    $rand = rand(0, count($ids) - 1);
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->in_story_id);
                    $url =  $ad_data->in_story_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 2:
                    $rand = rand(0, count($ids) - 1);
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->footer_id);
                    $url =  $ad_data->footer_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 3:
                    $rand = rand(0, count($ids) - 1);
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->sidebar_one_id);
                    $url =  $ad_data->sidebar_one_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 4:
                    $rand = rand(0, count($ids) - 1);
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->sidebar_two_id);
                    $url =  $ad_data->sidebar_two_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
            }
        endfor;

        echo json_encode($html);
        die;
    }
}
