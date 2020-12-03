<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Base;

class Shortcode extends BaseController
{
    public function register()
    {
        $this->activateShortcodes();
    }

    public function activateShortcodes()
    {
        add_shortcode('affiliate_adv', [$this, 'shortcodeContent']);
    }

    public function shortcodeContent($atts)
    {
        global $wpdb;
        extract(shortcode_atts(array(
            'number' => 1,
        ), $atts));

        $sql = 'SELECT ' . $wpdb->prefix . 'advertisements_dash.*, ' . $wpdb->prefix . 'users.user_nicename FROM ' . $wpdb->prefix . 'advertisements_dash INNER JOIN ' . $wpdb->prefix . 'users ON ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $wpdb->prefix . 'users.ID WHERE ' . $wpdb->prefix . 'advertisements_dash.id = ' . (int)$number;

        $result = $wpdb->get_row($sql);

        if ($result->status > 0) :
            $html = '<div class="container"><a href="' . $result->affiliate . '" target="_blank"><img src="' . $result->image . '"></a></div>';
        else :
            $html = '<div class="container" style="color:red;"><p>This Advertisement is not activated by the Administrator yet.</p></div>';
        endif;
        return $html;
    }
}
