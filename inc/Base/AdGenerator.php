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
            $sql1 = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE membership_type="ultra_diamond" AND status=1 AND ad_data IS NOT NULL ORDER BY RAND() LIMIT 3;';
            $r1 = $wpdb->get_results($sql1);

            $sql2 = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE membership_type="diamond" AND status=1 AND ad_data IS NOT NULL ORDER BY RAND() LIMIT 1;';
            $r2 = $wpdb->get_results($sql2);

            $sql3 = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE membership_type="emerald" AND status=1 AND ad_data IS NOT NULL ORDER BY RAND() LIMIT 1;';
            $r3 = $wpdb->get_results($sql3);

            $ids = array_merge($r1, $r2, $r3);
        } elseif ($_REQUEST['container'] == 'post') {
            if ((int)$_REQUEST['cat_id'] == 2) {
                $case = 2;
                $sql = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1 AND ad_data IS NOT NULL AND membership_type="ultra_diamond" ORDER BY RAND() LIMIT 5;';
                $ids = $wpdb->get_results($sql);
            } else {
                $case = 3;
                $sql1 = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE membership_type="ultra_diamond" AND status=1 AND ad_data IS NOT NULL ORDER BY RAND() LIMIT 3;';
                $r1 = $wpdb->get_results($sql1);

                $sql2 = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE membership_type="diamond" AND status=1 AND ad_data IS NOT NULL ORDER BY RAND() LIMIT 1;';
                $r2 = $wpdb->get_results($sql2);

                $sql3 = 'SELECT * FROM ' . $wpdb->prefix . 'advertisements_dash WHERE membership_type="emerald" AND status=1 AND ad_data IS NOT NULL ORDER BY RAND() LIMIT 1;';
                $r3 = $wpdb->get_results($sql3);

                $ids = array_merge($r1, $r2, $r3);
            }
        }
        // $ids = $wpdb->get_results($sql);
        $ad_types = ['banner', 'in_story', 'sidebar_one', 'sidebar_two', 'footer'];

        $html = [];
        for ($i = 0; $i < 5; $i++) :
            switch ($i) {
                case 0:
                    $rand = (count($ids) < 5 ) ? rand(0, count($ids) -1) : $i;
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->banner_id);
                    $url =  $ad_data->banner_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 1:
                    $rand = (count($ids) < 5 ) ? rand(0, count($ids) -1) : $i;
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->in_story_id);
                    $url =  $ad_data->in_story_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 2:
                    $rand = (count($ids) < 5 ) ? rand(0, count($ids) -1) : $i;
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->sidebar_one_id);
                    $url =  $ad_data->sidebar_one_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 3:
                    $rand = (count($ids) < 5 ) ? rand(0, count($ids) -1) : $i;
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->sidebar_two_id);
                    $url =  $ad_data->sidebar_two_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 4:
                    $rand = (count($ids) < 5 ) ? rand(0, count($ids) -1) : $i;
                    $ad_data = json_decode($ids[$rand]->ad_data);
                    $image = wp_get_attachment_url($ad_data->footer_id);
                    $url =  $ad_data->footer_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
            }
        endfor;

        echo json_encode($html);
        die;
    }
}
