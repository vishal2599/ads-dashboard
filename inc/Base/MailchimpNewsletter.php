<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Base;

use \AdvDashboard\Base\DrewMailChimp;


class MailchimpNewsletter extends BaseController
{
    public function register()
    {
        add_action("wp_ajax_340b_mailchimp_newsletter", [$this, "createMailchimpNewsletter"]);
        add_action("wp_ajax_nopriv_340b_mailchimp_newsletter", [$this, "createMailchimpNewsletter"]);

        add_action("wp_ajax_340b_mailchimp_newsletter_send", [$this, "sendNewsletterDrafts"]);
        add_action("wp_ajax_nopriv_340b_mailchimp_newsletter_send", [$this, "sendNewsletterDrafts"]);

        add_action("wp_ajax_340b_remove_mailchimp_drafts", [$this, "removeMailchimpDrafts"]);
        add_action("wp_ajax_nopriv_340b_remove_mailchimp_drafts", [$this, "removeMailchimpDrafts"]);

        add_action("wp_ajax_get_mailchimp_lists", [$this, "getMailchimpLists"]);

        // add_action("wp_ajax_get_list_segments", [$this, "getMailchimpSegments"]);
    }
    public function createMailchimpNewsletter()
    {

        if (!wp_verify_nonce($_REQUEST['nonce'], "mailchimp_340b_nonce")) {
            exit("No naughty business please");
        }

        update_option('340_mailchimp_key', $_REQUEST['api_key']);
        update_option('340_mailchimp_subject', $this->saveApostrophe($_REQUEST['subject']));
        update_option('340_mailchimp_newsletter_top_ad', $_REQUEST['newsletter_top_ad']);
        if( $_REQUEST['newsletter_middle_ad'] == '' ){
            update_option('340_mailchimp_newsletter_middle_ad', '0');
        } else {
            update_option('340_mailchimp_newsletter_middle_ad', $_REQUEST['newsletter_middle_ad']);
        }
        update_option('340_mailchimp_newsletter_bottom_ad', $_REQUEST['newsletter_bottom_ad']);
        update_option('340_mailchimp_newsletter_audience', $_REQUEST['newsletter_audience']);
        update_option('340_mailchimp_newsletter_type', $_REQUEST['newsletter_type']);
        update_option('340_mailchimp_newsletter_referer', $_REQUEST['newsletter_referer']);
        update_option('340_mailchimp_closing_message_subscribers', json_encode( [$this->saveApostrophe($_REQUEST['closing_message_subscribers'])] ));
        update_option('340_mailchimp_closing_message_members', json_encode( [$this->saveApostrophe($_REQUEST['closing_message_members'])] ));
        update_option('340_mailchimp_preview_text', $this->saveApostrophe($_REQUEST['preview_text']));

        update_option('340_target_subscribers', $_REQUEST['target_subscribers']);
        // $sponsored_content = [];
        // if( $_REQUEST['sponsor_title'] != ''){
        //     $sponsored_content['title'] = $this->saveApostrophe($_REQUEST['sponsor_title']);
        //     $sponsored_content['url'] = $_REQUEST['sponsor_url'];
        //     $sponsored_content['copy'] = $this->removeEmptyTags( $this->saveApostrophe($_REQUEST['sponsor_description']) );
        // }
        // update_option('340_sponsored_content', json_encode($sponsored_content));

        $articles = [];
        $provider = [];
        $spotlight = [];
        $marketing_graphic = [];

        $articles[0]['copy'] = $this->saveApostrophe($_REQUEST['article_copy_1']);
        $articles[1]['copy'] = $this->saveApostrophe($_REQUEST['article_copy_2']);

        if( $_REQUEST['article_title_2'] != ''){
            $articles[1]['title'] = $this->saveApostrophe($_REQUEST['article_title_2']);
            $articles[1]['url'] = $_REQUEST['article_url_2'];
            $articles[1]['copy'] = $this->saveApostrophe($_REQUEST['article_copy_2']);
        }

        if( $_REQUEST['spotlight_title'] != '' ){
            $spotlight['title'] = $this->saveApostrophe( $_REQUEST['spotlight_title'] );
            $spotlight['image'] = $_REQUEST['spotlight_image'];
            $spotlight['post'] = $_REQUEST['spotlight_post'];
        }
        if( $_REQUEST['provider_title'] != '' ){
            $provider['title'] = $this->saveApostrophe( $_REQUEST['provider_title'] );
            $provider['image'] = $_REQUEST['provider_image'];
            $provider['post'] = $_REQUEST['provider_post'];
        }
        $marketing_graphic['text'] = $this->saveApostrophe( $_REQUEST['marketing_graphic_text'] );
        $marketing_graphic['url'] = $this->saveApostrophe( $_REQUEST['marketing_graphic_url'] );
        $marketing_graphic['url_for_image'] = $this->saveApostrophe( $_REQUEST['marketing_graphic_url_for_image'] );
        $marketing_graphic['image'] = $this->saveApostrophe( $_REQUEST['marketing_graphic_image'] );

        update_option('340_mailchimp_articles', json_encode($articles));
        update_option('340_mailchimp_spotlight', json_encode($spotlight));
        update_option('340_mailchimp_provider', json_encode($provider));
        update_option('340_mailchimp_marketing_graphic', json_encode($marketing_graphic));

        $this->initiateNewsLetterCreation($_REQUEST['posts']);
    }

    public function initiateNewsLetterCreation($posts)
    {
        $draft_newsletters = [];
      

        $post_data = $this->getSelectedWordpressPosts($posts);

        $apiKey = get_option('340_mailchimp_key');
        $MailChimp = new DrewMailChimp($apiKey);

        $newsletter_subject_line = get_option('340_mailchimp_subject');
        // $list_id = 'dcbb58b00b';
        $list_id = get_option('340_target_subscribers');
        // echo $list_id; die;
        $preview_text = get_option('340_mailchimp_preview_text');

        // $segments = $MailChimp->get('/lists/' . $list_id . '/segments');
        // foreach ($segments['segments'] as $seg) {
            if( $list_id == '94452e12bd' ){
                $MailChimp->post("campaigns", [
                    'type' => 'regular',
                    'recipients' => [
                        'list_id' => $list_id,
                        'segment_opts' => [
                            'saved_segment_id' => (int)get_option('340_mailchimp_newsletter_audience')
                        ]
                    ],
                    'settings' => [
                        'subject_line' => $newsletter_subject_line,
                        'preview_text' => $preview_text,
                        'reply_to' => 'info@340breport.com',
                        'from_name' => '340B Report'
                        ]
                    ]);
                } else {
                    $MailChimp->post("campaigns", [
                        'type' => 'regular',
                        'recipients' => [
                            'list_id' => $list_id,

                        ],
                        'settings' => [
                            'subject_line' => $newsletter_subject_line,
                            'preview_text' => $preview_text,
                            'reply_to' => 'info@340breport.com',
                            'from_name' => '340B Report'
                            ]
                        ]);
                }
                    
            $html = $this->createNewsletterLayout($post_data['segment_1'], $post_data['segment_2']);
            $response = $MailChimp->getLastResponse();
            $responseObj = json_decode($response['body']);


            $template = $MailChimp->post('templates', [
                'name' => 'Custom HTML',
                'html' => $html
            ]);

            $result = $MailChimp->put('campaigns/' . $responseObj->id . '/content', [
                'template' => [
                    'id' => $template['id']
                ],
                'html' => $html
            ]);
            $draft_newsletters[] = $responseObj->id;
           if($_REQUEST['newsletter_referer'] == 'newsletter'){
                update_option('340_mailchimp_newsletter_drafts', json_encode($draft_newsletters));
                update_option('340_mailchimp_newsletter_posts', json_encode($posts));
           }else{
                    update_option('340_mailchimp_breaking_news_drafts', json_encode($draft_newsletters));
                    update_option('340_mailchimp_breaking_news_posts', json_encode($posts));
                    }
        $response = ['status' => 'success'];
        echo json_encode($response);
        die;
    }

    public function sendNewsletterDrafts()
    {
        if (!wp_verify_nonce($_REQUEST['nonce'], "mailchimp_340b_send_nonce")) {
            exit("No naughty business please");
        }
        $apiKey = get_option('340_mailchimp_key');
        $MailChimp = new DrewMailChimp($apiKey);
        $old_excludes = get_option('340_mailchimp_posts_exlude') != NULL ? json_decode(get_option('340_mailchimp_posts_exlude')): [];

        if( $_REQUEST['newsletter_referer'] == 'newsletter' ){
        
        $newsletter_drafts = json_decode(get_option('340_mailchimp_newsletter_drafts'));
	        foreach ($newsletter_drafts as $nd) {
	            $MailChimp->post('campaigns/' . $nd . '/actions/send');
	        }
					        update_option('340_mailchimp_newsletter_drafts', json_encode([]));
					        $excludes = array_merge(json_decode(get_option('340_mailchimp_newsletter_posts')), $old_excludes);
                            update_option('340_mailchimp_posts_exlude', json_encode($excludes));
        
					        
			        }
         else 
        {
            
	         $breaking_news_drafts = json_decode(get_option('340_mailchimp_breaking_news_drafts'));
		        foreach ($breaking_news_drafts as $nd) {
		            $MailChimp->post('campaigns/' . $nd . '/actions/send');
		        }
					        update_option('340_mailchimp_breaking_news_drafts', json_encode([]));
					        $excludes = array_merge(json_decode(get_option('340_mailchimp_breaking_news_posts')), $old_excludes);
                                                        update_option('340_mailchimp_posts_exlude', json_encode($excludes));
        
	    	}

                        update_option('340_mailchimp_newsletter_posts', json_encode([]));
                        update_option('340_mailchimp_breaking_news_posts', json_encode([]));
                        update_option('340_mailchimp_articles', json_encode([]));
                        update_option('340_mailchimp_newsletter_top_ad', '0');
                        update_option('340_mailchimp_newsletter_middle_ad', '0');
                        update_option('340_mailchimp_newsletter_bottom_ad', '0');
                        update_option('340_mailchimp_newsletter_type', 'none');

        $response = ['status' => 'success'];
        echo json_encode($response);
        die;
    }

    public function removeMailchimpDrafts()
    {
        if (!wp_verify_nonce($_REQUEST['nonce'], "340b_remove_mailchimp_drafts_nonce")) {
            exit("No naughty business please");
        }

        $apiKey = get_option('340_mailchimp_key');
        $MailChimp = new DrewMailChimp($apiKey);
        if( $_REQUEST['newsletter_referer'] == 'newsletter' ){
				        $newsletter_drafts = json_decode(get_option('340_mailchimp_newsletter_drafts'));

				        foreach ($newsletter_drafts as $nd) {
				            $MailChimp->delete('campaigns/' . $nd);
				        }
                          update_option('340_mailchimp_newsletter_drafts', json_encode([]));
                          update_option('340_mailchimp_newsletter_posts', json_encode([]));
          
		}else{
			        $breaking_news_drafts = json_decode(get_option('340_mailchimp_breaking_news_drafts'));

			        foreach ($breaking_news_drafts as $nd) {
			            $MailChimp->delete('campaigns/' . $nd);
			        }
                    update_option('340_mailchimp_breaking_news_drafts', json_encode([]));
                    update_option('340_mailchimp_breaking_news_posts', json_encode([]));
        
			        
}       
        update_option('340_mailchimp_articles', json_encode([]));
        update_option('340_mailchimp_newsletter_top_ad', '0');
        update_option('340_mailchimp_newsletter_middle_ad', '0');
        update_option('340_mailchimp_newsletter_bottom_ad', '0');

        $response = ['status' => 'success'];
        echo json_encode($response);
        die;
    }

    public function removeEmptyTags($string)
    {
        $pattern = "/<p>/";
        $string = preg_replace($pattern, '', $string);
        $pattern = "/<\\/p[^>]*>/";
        $string = preg_replace($pattern, '', $string);
        $string = '<p style="margin: 10px 0;padding: 0;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #202020;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;">'.$string.'</p>';
        return $string;
    }

    public function makeSponsoredBold($excerpt)
    {
        $excerpt = str_replace('SPONSORED CONTENT', '<strong>SPONSORED CONTENT</strong><br>', $excerpt);
        return $excerpt;
    }

    public function getSelectedWordpressPosts($posts)
    {
        $args = [
            'post_type' => 'post',
            'post__in' => $posts,
            'post_status' => 'publish',
        ];
        $sponsored_content = json_decode(get_option('340_sponsored_content'));
        $additional_posts = json_decode(get_option('340_mailchimp_articles'));


        $post_query = new \WP_Query($args);
        // $post_data = '<div class="posts-wrapper">';
        $post_data = [];

        $i = 0;

        while ($post_query->have_posts()) :
            $post_query->the_post();
            $excerpt = wp_strip_all_tags(wp_trim_excerpt());
            $bracket_dots = strpos($excerpt, '[...]');
            $filtered_excerpt = $this->makeSponsoredBold($excerpt);
            if( $bracket_dots == false ){
                $filtered_excerpt = $this->makeSponsoredBold( str_replace('...', '[...]', $excerpt) );
            }
                $divider = ($i == 0 && $sponsored_content->title == '' && $additional_posts[0]->copy == '') ? '' : '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnDividerBlockOuter"> <tr> <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span> </td></tr></tbody> </table><!-- <td class="mcnDividerBlockInner" style="padding: 18px;"><hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;"/>--> </td></tr></tbody> </table>';

                $post_data[] = $divider.'<table class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnTextBlockOuter"> <tr> <td class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" valign="top"><!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr><![endif]--><!--[if mso]><td valign="top" width="600" style="width:600px;"><![endif]--> <table style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnTextContentContainer" width="100%" cellspacing="0" cellpadding="0" border="0" align="left"> <tbody> <tr> <td class="mcnTextContent" style="padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #202020;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;" valign="top"> <div class="blog_post_title"> <div class="blog_post_title"> <h1 style="display: block;margin: 0;padding: 0;color: #202020;font-family: Helvetica;font-size: 26px;font-style: normal;font-weight: bold;line-height: 125%;letter-spacing: normal;text-align: left;"> ' . get_the_title() . ' </h1> </div></div><p style="margin: 10px 0;padding: 0;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #202020;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;"> ' . wp_trim_excerpt() . '</p></td></tr></tbody> </table><!--[if mso]></td><![endif]--><!--[if mso]></tr></table><![endif]--> </td></tr></tbody> </table> <table class="mcnButtonBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnButtonBlockOuter"> <tr> <td style="padding-top: 0;padding-right: 18px;padding-bottom: 18px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnButtonBlockInner" valign="top" align="left"> <table class="mcnButtonContentContainer" style="border-collapse: separate !important;border-radius: 4px;background-color: #2BAADF;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td class="mcnButtonContent" style="font-family: Arial;font-size: 16px;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" valign="middle" align="center"> <a class="mcnButton " title="Read More" href="' . get_permalink() . '" target="_blank" style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;display: block;">Read More</a> </td></tr></tbody> </table> </td></tr></tbody> </table>';
                $i++;
        endwhile;
        // foreach($additional_posts as $adp){
        //     $post_data .= '<div class="single-post" style="text-align:center;"><a class="post-data" href="' . $adp->url . '" style="text-decoration:none;text-align:center;"><h2 style="color: #000000d4;max-width: 100%;text-align:center;">' . $adp->title . '</h2><div class="copy"><p style="font-size: 1rem;color: #0000008c;max-width: 100%;">' . $adp->copy . '</p></div></a></div>';
        // }
        // $post_data .= '</div>';

        // $apiKey = '8cd11fca91e85ff7e53c2e565528b2c6-us5';
        $response = [];
        $post_data_1 = '';
        $post_data_2 = '';
        // $post_half = count($post_data) / 2;
        for ($i = 0; $i < 2; $i++) {
            // $post_data_1 .= $post_data[0];
            $post_data_1 .= $post_data[$i];
        }
        $response['segment_1'] = $post_data_1;

        for ($j = 2; $j < count($post_data); $j++) {
            $post_data_2 .= $post_data[$j];
        }
        $response['segment_2'] = $post_data_2;

        return $response;
    }

    public function createNewsletterLayout($segment_1, $segment_2)
    {
        global $wpdb;
        $additional_posts = json_decode(get_option('340_mailchimp_articles'));
        $audience = get_option('340_mailchimp_newsletter_audience');
        $type = get_option('340_mailchimp_newsletter_type');
        $provider = json_decode(get_option('340_mailchimp_provider'));
        $spotlight = json_decode(get_option('340_mailchimp_spotlight'));
        $marketing_graphic = json_decode(get_option('340_mailchimp_marketing_graphic'));

        if( $audience == "3366313" ){
            $closing_message = json_decode(get_option('340_mailchimp_closing_message_subscribers'))[0];
        } elseif( $audience == "3374743" ) {
            $closing_message = json_decode(get_option('340_mailchimp_closing_message_members'))[0];
        }

        $sql = 'SELECT ad_data, company_data FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1 AND JSON_EXTRACT(ad_data,"$.newsletter_id") != "NULL" AND JSON_EXTRACT(ad_data, TRIM("$.newsletter_id")) != "" ORDER BY RAND() LIMIT 3';
        $ads = $wpdb->get_results($sql);

        if( get_option('340_mailchimp_newsletter_top_ad') == 0 ){
            $top_ad = json_decode($ads[0]->ad_data);
            $top_company = json_decode($ads[0]->company_data);
        } else {
            $selected_top_ad = get_option('340_mailchimp_newsletter_top_ad');
            $sql_query = 'SELECT ad_data FROM ' . $wpdb->prefix . 'advertisements_dash WHERE id='.$selected_top_ad;
            $result = $wpdb->get_results($sql_query);

            $top_ad = json_decode($result[0]->ad_data);
            $top_company = json_decode($result[0]->company_data);
        }

        // $top_ad = json_decode($ads[0]->ad_data);
        // $top_company = json_decode($ads[0]->company_data);

        if( get_option('340_mailchimp_newsletter_middle_ad') == 0 ){
            // $middle_ad = json_decode($ads[1]->ad_data);
            // $middle_company = json_decode($ads[1]->company_data);
            $middle_ad = 0;
        } else {
            $selected_middle_ad = get_option('340_mailchimp_newsletter_middle_ad');
            $sql_query = 'SELECT ad_data FROM ' . $wpdb->prefix . 'advertisements_dash WHERE id='.$selected_middle_ad;
            $result = $wpdb->get_results($sql_query);

            $middle_ad = json_decode($result[0]->ad_data);
            $middle_company = json_decode($result[0]->company_data);
        }

        if( get_option('340_mailchimp_newsletter_bottom_ad') == 0 ){
            $bottom_ad = json_decode($ads[2]->ad_data);
            $bottom_company = json_decode($ads[2]->company_data);
        } else {
            $selected_bottom_ad = get_option('340_mailchimp_newsletter_bottom_ad');
            $sql_query = 'SELECT ad_data FROM ' . $wpdb->prefix . 'advertisements_dash WHERE id='.$selected_bottom_ad;
            $result = $wpdb->get_results($sql_query);

            $bottom_ad = json_decode($result[0]->ad_data);
            $bottom_company = json_decode($result[0]->company_data);
        }


        // $bottom_ad = json_decode($ads[2]->ad_data);
        // $bottom_company = json_decode($ads[2]->company_data);

        

        $html = '<!--*|IF:MC_PREVIEW_TEXT|*--><!--[if !gte mso 9]><!----><span class="mcnPreviewText" style="display:none; font-size:0px; line-height:0px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; visibility:hidden; mso-hide:all;">*|MC_PREVIEW_TEXT|*</span><!--<![endif]--><!--*|END:IF|*--><table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height: 100%;margin: 0;padding: 0;width: 100%;background-color: #FAFAFA;"> <tbody> <tr> <td align="center" valign="top" id="bodyCell" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height: 100%;margin: 0;padding: 0;width: 100%;border-top: 0;"> <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td align="center" valign="top" id="templateHeader" style="background:#FFFFFF none no-repeat center/cover;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #FFFFFF;background-image: none;background-repeat: no-repeat;background-position: center;background-size: cover;border-top: 0;border-bottom: 0;padding-top: 9px;padding-bottom: 0;"><!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;"><tr><td align="center" valign="top" width="600" style="width:600px;"><![endif]--> <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;max-width: 600px !important;"> <tbody> <tr> <td valign="top" class="headerContainer" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">';

        $top_ad_block = '<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnImageBlockOuter"> <tr> <td valign="top" style="padding: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner"> <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td class="mcnImageContent" valign="top" style="padding-right: 9px;padding-left: 9px;padding-top: 0;padding-bottom: 0;text-align: center;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <a href="' . $top_ad->newsletter_url . '" title="" class="" target="_blank" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <img align="center" alt="' . $top_company->company_name . '" src="' . wp_get_attachment_url($top_ad->newsletter_id) . '" width="564" style="max-width: 600px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage"> </a> </td></tr></tbody> </table> </td></tr></tbody> </table>';

        $html .= $top_ad_block;

        $logo_block = '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnDividerBlockOuter"> <tr> <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span> </td></tr></tbody> </table><!-- <td class="mcnDividerBlockInner" style="padding: 18px;"><hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;"/>--> </td></tr></tbody> </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnImageBlockOuter"> <tr> <td valign="top" style="padding: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner"> <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td class="mcnImageContent" valign="top" style="padding-right: 9px;padding-left: 9px;padding-top: 0;padding-bottom: 0;text-align: center;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <a href="https://340breport.com/" title="" class="" target="_blank" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <img align="center" alt="340B Report" src="https://340breport.com/wp-content/uploads/2020/09/unnamed.png" width="564" style="max-width: 1100px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage"> </a> </td></tr></tbody> </table> </td></tr></tbody> </table>';

        $html .= $logo_block;
        
        if( get_option('340_mailchimp_newsletter_referer') == 'breaking_news' || get_option('340_mailchimp_newsletter_referer') == 'newsletter' && get_option('340_mailchimp_newsletter_type') != 'none' ){
            $news_type = get_option('340_mailchimp_newsletter_type');
            $background = 'background: #fff;';
            $text_color = 'color: #e7a13c;';
            if( $news_type == 'Breaking News' ){
                $background = 'background: #e6a13c;';
                $text_color = 'color: #265989;';
            } elseif( $news_type == 'News Alert' ){
                $background = 'background:#265989;';
                $text_color = 'color: #e6a13c;';
            }

            $html .= '<table class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnTextBlockOuter"> <tr> <td class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" valign="top"><!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr><![endif]--><!--[if mso]><td valign="top" width="600" style="width:600px;"><![endif]--> <table style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnTextContentContainer" width="100%" cellspacing="0" cellpadding="0" border="0" align="left"> <tbody> <tr> <td class="mcnTextContent" style="padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #202020;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;" valign="top"> <div class="blog_post_title"> <div class="blog_post_title"> <h1 style="display: block;margin: 0;font-family: Helvetica;font-style: normal;line-height: 125%;letter-spacing: normal;'.$text_color.' font-size: 42px; font-weight: 900; '. $background .' padding: 16px; text-transform: uppercase; text-align: center;"> ' . $news_type . ' </h1> </div></div></td></tr></tbody> </table><!--[if mso]></td><![endif]--><!--[if mso]></tr></table><![endif]--> </td></tr></tbody> </table>';
        }

        // $html .= '<div class="posts-wrapper">';
        if ($additional_posts[0]->copy != '') {
            $html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnTextBlockOuter"> <tr> <td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr><![endif]--><!--[if mso]><td valign="top" width="600" style="width:600px;"><![endif]--> <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer"> <tbody> <tr> <td valign="top" class="mcnTextContent" style="padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #202020;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;">' . $additional_posts[0]->copy . '</td></tr></tbody> </table><!--[if mso]></td><![endif]--><!--[if mso]></tr></table><![endif]--> </td></tr></tbody> </table>';
        } 
        // else {
        //     $sponsored_content = json_decode(get_option('340_sponsored_content'));
        //     $html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnTextBlockOuter"> <tr> <td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr><![endif]--><!--[if mso]><td valign="top" width="600" style="width:600px;"><![endif]--> <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer"> <tbody> <tr> <td valign="top" class="mcnTextContent" style="padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #202020;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;"> <h1 class="null" style="display: block;margin: 0;padding: 0;color: #202020;font-family: Helvetica;font-size: 26px;font-style: normal;font-weight: bold;line-height: EO Ted S125%;letter-spacing: normal;text-align: left;">' . $sponsored_content->title . '</h1>' . $sponsored_content->copy . '</td></tr></tbody> </table><!--[if mso]></td><![endif]--><!--[if mso]></tr></table><![endif]--> </td></tr></tbody> </table>';
        // }

        
        $html .= $segment_1;

        if( $middle_ad != 0 ){
            $html .= '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnDividerBlockOuter"> <tr> <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span> </td></tr></tbody> </table><!-- <td class="mcnDividerBlockInner" style="padding: 18px;"><hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;"/>--> </td></tr></tbody> </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnImageBlockOuter"> <tr> <td valign="top" style="padding: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner"> <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td class="mcnImageContent" valign="top" style="padding-right: 9px;padding-left: 9px;padding-top: 0;padding-bottom: 0;text-align: center;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <a href="' . $middle_ad->newsletter_url . '" title="" class="" target="_blank" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <img align="center" alt="' . $middle_company->company_name . '" src="' . wp_get_attachment_url($middle_ad->newsletter_id) . '" width="564" style="max-width: 600px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage"> </a> </td></tr></tbody> </table> </td></tr></tbody> </table>';
        }

        $html .= $segment_2;

        if ($additional_posts[1]->copy != '') {
            $html .= '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnDividerBlockOuter"> <tr> <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span> </td></tr></tbody> </table><!-- <td class="mcnDividerBlockInner" style="padding: 18px;"><hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;"/>--> </td></tr></tbody> </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnTextBlockOuter"> <tr> <td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr><![endif]--><!--[if mso]><td valign="top" width="600" style="width:600px;"><![endif]--> <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer"> <tbody> <tr> <td valign="top" class="mcnTextContent" style="padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #202020;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;">' .  $additional_posts[1]->copy . '</td></tr></tbody> </table><!--[if mso]></td><![endif]--><!--[if mso]></tr></table><![endif]--> </td></tr></tbody> </table>';
        }

        if( !empty($spotlight) && $spotlight->title != '' ){
            $html .= '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnDividerBlockOuter"> <tr> <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span> </td></tr></tbody> </table><!-- <td class="mcnDividerBlockInner" style="padding: 18px;"><hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;"/>--> </td></tr></tbody> </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnImageBlockOuter"> <tr> <td valign="top" style="padding: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner"> <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td class="mcnImageContent" valign="top" style="padding-right: 9px;padding-left: 9px;padding-top: 0;padding-bottom: 0;text-align: left;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <img align="left" alt="340B Report" src="https://340breport.com/wp-content/uploads/2021/07/spotlight.png" width="564" style="max-width: 300px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage"> </td></tr></tbody> </table> </td></tr></tbody> </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnImageBlockOuter"> <tr> <td valign="top" style="padding: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner"> <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td class="mcnImageContent" valign="top" style="padding-right: 9px;padding-left: 9px;padding-top: 0;padding-bottom: 0;text-align: center;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;text-align: left;"><img align="center" alt="340B Report" src="'. wp_get_attachment_url( $spotlight->image).'" width="564" style="max-width: 148px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage"> </td></tr></tbody> </table> </td></tr></tbody> </table><table class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnTextBlockOuter"> <tr> <td class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" valign="top"><!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr><![endif]--><!--[if mso]><td valign="top" width="600" style="width:600px;"><![endif]--> <table style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnTextContentContainer" width="100%" cellspacing="0" cellpadding="0" border="0" align="left"> <tbody> <tr> <td class="mcnTextContent" style="padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #202020;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;" valign="top"> <div class="blog_post_title"> <div class="blog_post_title" style="margin-bottom:10px;"> <h1 style="display: block;margin: 0;padding: 0;color: #202020;font-family: Helvetica;font-size: 26px;font-style: normal;font-weight: bold;line-height: 125%;letter-spacing: normal;text-align: left;">Spotlight on 340B Industry Leader </h1> </div></div><h1 style="display: block;margin: 0;padding: 0;color: #202020;font-family: Helvetica;font-size: 20px;font-style: normal;font-weight: bold;line-height: 125%;letter-spacing: normal;text-align: left;margin-bottom:10px;"> ' . $spotlight->title . ' </h1></td></tr></tbody> </table><!--[if mso]></td><![endif]--><!--[if mso]></tr></table><![endif]--> </td></tr></tbody> </table> <table class="mcnButtonBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnButtonBlockOuter"> <tr> <td style="padding-top: 0;padding-right: 18px;padding-bottom: 18px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnButtonBlockInner" valign="top" align="left"> <table class="mcnButtonContentContainer" style="border-collapse: separate !important;border-radius: 4px;background-color: #2BAADF;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td class="mcnButtonContent" style="font-family: Arial;font-size: 16px;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" valign="middle" align="center"> <a class="mcnButton " title="Read More" href="' . get_permalink($spotlight->post) . '" target="_blank" style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;display: block;">Read More</a> </td></tr></tbody> </table> </td></tr></tbody> </table>';
        }

        if( !empty($provider) && $provider->title != '' ){
            $html .= '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnDividerBlockOuter"> <tr> <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span> </td></tr></tbody> </table><!-- <td class="mcnDividerBlockInner" style="padding: 18px;"><hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;"/>--> </td></tr></tbody> </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnImageBlockOuter"> <tr> <td valign="top" style="padding: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner"> <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td class="mcnImageContent" valign="top" style="padding-right: 9px;padding-left: 9px;padding-top: 0;padding-bottom: 0;text-align: left;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <img align="left" alt="340B Report" src="https://340breport.com/wp-content/uploads/2021/07/spotlight.png" width="564" style="max-width: 300px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage"> </td></tr></tbody> </table> </td></tr></tbody> </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnImageBlockOuter"> <tr> <td valign="top" style="padding: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner"> <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td class="mcnImageContent" valign="top" style="padding-right: 9px;padding-left: 9px;padding-top: 0;padding-bottom: 0;text-align: center;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;text-align: left;"><img align="center" alt="340B Report" src="'. wp_get_attachment_url( $provider->image).'" width="564" style="max-width: 148px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage"> </td></tr></tbody> </table> </td></tr></tbody> </table><table class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnTextBlockOuter"> <tr> <td class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" valign="top"><!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr><![endif]--><!--[if mso]><td valign="top" width="600" style="width:600px;"><![endif]--> <table style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnTextContentContainer" width="100%" cellspacing="0" cellpadding="0" border="0" align="left"> <tbody> <tr> <td class="mcnTextContent" style="padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #202020;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;" valign="top"> <div class="blog_post_title"> <div class="blog_post_title" style="margin-bottom:10px;"> <h1 style="display: block;margin: 0;padding: 0;color: #202020;font-family: Helvetica;font-size: 26px;font-style: normal;font-weight: bold;line-height: 125%;letter-spacing: normal;text-align: left;">Spotlight on 340B Provider Leader </h1> </div></div><h1 style="display: block;margin: 0;padding: 0;color: #202020;font-family: Helvetica;font-size: 20px;font-style: normal;font-weight: bold;line-height: 125%;letter-spacing: normal;text-align: left;margin-bottom:10px;"> ' . $provider->title . ' </h1></td></tr></tbody> </table><!--[if mso]></td><![endif]--><!--[if mso]></tr></table><![endif]--> </td></tr></tbody> </table> <table class="mcnButtonBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnButtonBlockOuter"> <tr> <td style="padding-top: 0;padding-right: 18px;padding-bottom: 18px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnButtonBlockInner" valign="top" align="left"> <table class="mcnButtonContentContainer" style="border-collapse: separate !important;border-radius: 4px;background-color: #2BAADF;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td class="mcnButtonContent" style="font-family: Arial;font-size: 16px;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" valign="middle" align="center"> <a class="mcnButton " title="Read More" href="' . get_permalink($provider->post) . '" target="_blank" style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;display: block;">Read More</a> </td></tr></tbody> </table> </td></tr></tbody> </table>';
        }

        
            $html .= '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnDividerBlockOuter"> <tr> <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span> </td></tr></tbody> </table><!-- <td class="mcnDividerBlockInner" style="padding: 18px;"><hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;"/>--> </td></tr></tbody> </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnImageBlockOuter"> <tr> <td valign="top" style="padding: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner"> <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td class="mcnImageContent" valign="top" style="padding-right: 9px;padding-left: 9px;padding-top: 0;padding-bottom: 0;text-align: center;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;text-align: left;">
                <a href="' . $marketing_graphic->url_for_image . '"><img align="center" alt="340B Report" src="'. wp_get_attachment_url( $marketing_graphic->image).'" width="564" style="padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage"></a>
                </td></tr></tbody> </table> </td></tr></tbody> </table>';
            
            if($marketing_graphic->text != ''){
                $html .= '<table class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnTextBlockOuter"> <tr> <td class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" valign="top"><table class="mcnButtonBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnButtonBlockOuter"> <tr> <td style="padding-top: 0;padding-right: 18px;padding-bottom: 18px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnButtonBlockInner" valign="top" align="left"> <table class="mcnButtonContentContainer" style="border-collapse: separate !important;border-radius: 4px;background-color: #255888;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;width:100%;" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td class="mcnButtonContent" style="font-family: Arial;font-size: 16px;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" valign="middle" align="center"> <a class="mcnButton " title="'.$marketing_graphic->text.'" href="' . $marketing_graphic->url . '" target="_blank" style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;display: block;">'.$marketing_graphic->text.'</a> </td></tr></tbody> </table> </td></tr></tbody> </table>';
            }
          
        

        $html .= '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnDividerBlockOuter"> <tr> <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span> </td></tr></tbody> </table><!-- <td class="mcnDividerBlockInner" style="padding: 18px;"><hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;"/>--> </td></tr></tbody> </table> <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnBoxedTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><!--[if gte mso 9]><table align="center" border="0" cellspacing="0" cellpadding="0" width="100%"><![endif]--> <tbody class="mcnBoxedTextBlockOuter"> <tr> <td valign="top" class="mcnBoxedTextBlockInner" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><!--[if gte mso 9]><td align="center" valign="top" "><![endif]--> <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnBoxedTextContentContainer"> <tbody> <tr> <td style="padding-top: 9px;padding-left: 18px;padding-bottom: 9px;padding-right: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table border="0" cellspacing="0" class="mcnTextContentContainer" width="100%" style="min-width: 100% !important;border: 2px solid;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td valign="top" class="mcnTextContent" style="padding: 18px;font-family: Helvetica;font-size: 16px;font-weight: normal;text-align: left;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #202020;line-height: 150%;"> '.$closing_message.' </td></tr></tbody> </table> </td></tr></tbody> </table><!--[if gte mso 9]></td><![endif]--><!--[if gte mso 9]></tr></table><![endif]--> </td></tr></tbody> </table>';

        // $html .= '</div>';


        $html .= '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody class="mcnDividerBlockOuter"> <tr> <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span> </td></tr></tbody> </table><!-- <td class="mcnDividerBlockInner" style="padding: 18px;"><hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;"/>--> </td></tr></tbody> </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody class="mcnImageBlockOuter"> <tr> <td valign="top" style="padding: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner"> <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <tbody> <tr> <td class="mcnImageContent" valign="top" style="padding-right: 9px;padding-left: 9px;padding-top: 0;padding-bottom: 0;text-align: center;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <a href="' . $bottom_ad->newsletter_url . '" title="" class="" target="_blank" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <img align="center" alt="' . $bottom_company->company_name . '" src="' . wp_get_attachment_url($bottom_ad->newsletter_id) . '" width="564" style="max-width: 600px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage"> </a> </td></tr></tbody> </table> </td></tr></tbody> </table>';

        $html .= '<table class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;" width="100%" cellspacing="0" cellpadding="0" border="0"><tbody class="mcnDividerBlockOuter"><tr><td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <span></span></td></tr></tbody></table></td></tr></tbody></table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--><!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #656565;font-family: Helvetica;font-size: 12px;line-height: 150%;text-align: center;"><em>Copyright © '.date("Y").' 340B Report LLC All rights reserved.</em><br> <br> <br> Want to change how you receive these emails?<br> You can <a href="*|UPDATE_PROFILE|*" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #656565;font-weight: normal;text-decoration: underline;">update your preferences</a> or <a href="*|UNSUB|*" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #656565;font-weight: normal;text-decoration: underline;">unsubscribe from this list</a>.</td></tr></tbody></table> <!--[if mso]></td> <![endif]--><!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table>';

        $html .= '</td></tr></tbody> </table><!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--> </td></tr></tbody> </table> </td></tr></tbody></table>';

        return $html;
    }

    public function getMailchimpLists()
    {
        $apiKey = get_option('340_mailchimp_key');
        $MailChimp = new DrewMailChimp($apiKey);
        
        $lists = $MailChimp->get('/lists');
        echo json_encode($lists);
        die;
    }

    // public function getMailchimpSegments()
    // {
    //     $apiKey = get_option('340_mailchimp_key');
    //     $MailChimp = new DrewMailChimp($apiKey);

    //     $list_id = $_REQUEST['list_id'];

    //     $segments = $MailChimp->get('/lists/' . $list_id . '/segments');
    //     echo json_encode($segments);
    //     die;
    // }
}
