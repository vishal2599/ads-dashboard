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
                    $image = wp_get_attachment_url($ids[$rand]->banner_id);
                    $url =  $ids[$rand]->banner_url;
                    if ($ids[$rand]->membership_type == 'emerald' && $case == 1) {
                        $html[$ad_types[$i]] .= '&nbsp;';
                    } else {
                        $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    }
                    break;
                case 1:
                    $rand = rand(0, count($ids) - 1);
                    $image = wp_get_attachment_url($ids[$rand]->in_story_id);
                    $url = $ids[$rand]->in_story_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 2:
                    $rand = rand(0, count($ids) - 1);
                    $image = wp_get_attachment_url($ids[$rand]->footer_id);
                    $url = $ids[$rand]->footer_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 3:
                    $rand = rand(0, count($ids) - 1);
                    $image = wp_get_attachment_url($ids[$rand]->sidebar_one_id);
                    $url = $ids[$rand]->sidebar_one_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
                case 4:
                    $rand = rand(0, count($ids) - 1);
                    $image = wp_get_attachment_url($ids[$rand]->sidebar_two_id);
                    $url = $ids[$rand]->sidebar_two_url;
                    $html[$ad_types[$i]] .= '<div class="container advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $url . '"><img src="' . $image . '"></a></div>';
                    break;
            }
        endfor;

        echo json_encode($html);
        die;
    }
}
