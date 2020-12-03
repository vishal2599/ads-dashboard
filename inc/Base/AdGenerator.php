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
        $sql = 'SELECT id FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1';
        $ids = $wpdb->get_col($sql);

        $random_id = $ids[array_rand($ids)];
        $sql2 = "SELECT * FROM " . $wpdb->prefix . "advertisements_dash WHERE id=$random_id";
        $data = $wpdb->get_row($sql2);

        $html = [];
        $ad_types = ['banner', 'in_story', 'footer', 'sidebar_one', 'sidebar_two'];
        $banner = wp_get_attachment_url($data->banner_id);
        $in_story = wp_get_attachment_url($data->in_story_id);
        $footer = wp_get_attachment_url($data->footer_id);
        $sidebar_one = wp_get_attachment_url($data->sidebar_one_id);
        $sidebar_two = wp_get_attachment_url($data->sidebar_two_id);
        $affiliate = $data->affiliate;
        for ($i = 0; $i < 5; $i++) :
            switch ($ad_types[$i]) {
                case 'banner':
                    $image = $banner;
                    break;
                case 'in_story':
                    $image = $in_story;
                    break;
                case 'footer':
                    $image = $footer;
                    break;
                case 'sidebar_one':
                    $image = $sidebar_one;
                    break;
                case 'sidebar_two':
                    $image = $sidebar_two;
                    break;

                default:
                    $image = $banner;
                    break;
            }
            $html[$ad_types[$i]] .= '<div class="advDashboard ' . $ad_types[$i] . '"><a target="_blank" href="' . $affiliate . '"><img src="' . $image . '"></a></div>';
        endfor;

        echo json_encode($html);
        die;
    }
}
