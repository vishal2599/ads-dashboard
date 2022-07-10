<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php
global $wpdb;
//print_r(get_option('340_target_subscribers'));
$args = [
    'post_type' => 'post',
    'posts_per_page' => -1,
    'post__not_in' => json_decode(get_option('340_mailchimp_posts_exlude')),
    'post_status' => 'publish'
];

$closing_message_subscribers = str_replace('\\', '', json_decode(get_option('340_mailchimp_closing_message_subscribers'))[0]);
$closing_message_members = str_replace('\\', '',json_decode(get_option('340_mailchimp_closing_message_members'))[0]);
$preview_text = get_option('340_mailchimp_preview_text');
$additional_posts = json_decode(get_option('340_mailchimp_articles'));
// echo '<pre>';
// print_r($additional_posts);
$all_posts = new \WP_Query($args);

if( $_GET['page'] == '340b_breaking_news' ){
    $company_middle = $wpdb->get_results('SELECT id, company_data FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1 AND membership_type="ultra_diamond" AND JSON_EXTRACT(ad_data,"$.newsletter_id") != "NULL" AND JSON_EXTRACT(ad_data, TRIM("$.newsletter_id")) != ""');
    $two_ads = $company_middle;
    $middle = $company_middle;


} else {
    $two_ads = $wpdb->get_results('SELECT id, company_data FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1 AND membership_type<>"emerald" AND JSON_EXTRACT(ad_data,"$.newsletter_id") != "NULL" AND JSON_EXTRACT(ad_data, TRIM("$.newsletter_id")) != ""');
    $middle = $wpdb->get_results('SELECT id, company_data FROM ' . $wpdb->prefix . 'advertisements_dash WHERE status=1 AND JSON_EXTRACT(ad_data,"$.newsletter_id") != "NULL" AND JSON_EXTRACT(ad_data, TRIM("$.newsletter_id")) != ""');


}

if($_GET['page'] != '340b_mailchimp_newsletter'){
$newsletters_in_draft = (empty(json_decode(get_option('340_mailchimp_breaking_news_drafts'))) && empty(json_decode(get_option('340_mailchimp_breaking_news_posts')))) ? false : true;
}else{
$newsletters_in_draft = (empty(json_decode(get_option('340_mailchimp_newsletter_drafts'))) && empty(json_decode(get_option('340_mailchimp_newsletter_posts')))) ? false : true;
}


//print_r(get_option('340_mailchimp_breaking_news_drafts'));
   //print_r(get_option('340_mailchimp_breaking_news_posts'));
   
//$newsletters_in_draft = (empty(json_decode(get_option('340_mailchimp_newsletter_drafts'))) && empty(json_decode(get_option('340_mailchimp_posts')))) ? false : true;

//if($newsletters_in_draft === true){

//$newsletters_in_draft = (empty(json_decode(get_option('340_mailchimp_newsletter_drafts'))) && empty(json_decode(get_option('340_mailchimp_posts')))) ? false : true;
//}
$audience = get_option('340_mailchimp_newsletter_audience');
//print_r($audience);
// echo '<pre>';
//  print_r(json_decode( get_option('340_mailchimp_articles')));
$all_posts = new \WP_Query($args);

$spotlight_args = [
    'post_type' => 'post',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'tax_query' => array(
        array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => [ 'spotlights' ]
        )
    )
];
$all_spotlights = new \WP_Query($spotlight_args);
$spotlight = json_decode(get_option('340_mailchimp_spotlight'));
$provider_args = [
    'post_type' => 'post',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'tax_query' => array(
        array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => [ '340b-provider' ]
        )
    )
];
$all_providers = new \WP_Query($provider_args);
$provider = json_decode(get_option('340_mailchimp_provider'));
$marketing_graphic = json_decode(get_option('340_mailchimp_marketing_graphic'));
?>
<style>
    .adv-create-ad .form-fields input[type="text"] {
        width: 400px;
    }
</style>
<div class="adv-create-ad wrap 340b-newsletter-posts">
    <h1 class="wp-heading-inline">Edit the NewsLetter Configuration</h1>

    <form class="adv_admin_edit adv-form mailchimp-newsletter" method="post" name='adv_dashboard_edit_form'>
    <?php if( $_REQUEST['page'] != '340b_mailchimp_newsletter' ): ?>
        <input type="hidden" name="newsletter_referer" value="breaking_news">
    <?php else: ?>
        <input type="hidden" name="newsletter_referer" value="newsletter">
    <?php endif; ?>
        <div class="advertise-add <?php echo ($newsletters_in_draft) ? ' has-drafts' : ''; ?>">
            <div class="form-fields">
                <h3 class="adv-headings">API key: </h3><br><br>
                <?php $api_key = get_option('340_mailchimp_key'); ?>
                <input type="text" name="340_mailchimp_key" placeholder="Enter Api Key" value="<?php echo ($api_key) ? $api_key : ''; ?>">
            </div>
            <div class="form-fields subscribers-wrap">
                <h3 class="adv-headings">Target Subscribers: </h3><br><br>
                <select name="target_subscribers" name="target_subscribers" class="target_subscribers" required>
                    <option value="">Choose subscribers</option>
                </select>
                <span class="loading"><img src="<?php echo $this->plugin_url.'/assets/images/spinning-circles.svg'; ?>" alt=""></span>
            </div>
            <!-- <div class="form-fields segments-wrap">
            </div> -->
            <div class="form-fields">
                <h3 class="adv-headings">Audience: </h3><br><br>
                <select name="newsletter_audience" name="newsletter_audience" class="newsletter_audience" required>
                    <option value="">Choose an audience segment</option>
                    <option value="3366313"<?php echo ($audience == "3366313") ? ' selected' : ''; ?>>Not on a Plan in Memberful</option>
                    <option value="3374743"<?php echo ($audience == "3374743") ? ' selected' : ''; ?>>Paid Subscribers</option>
                </select>
            </div>
            <?php if( $_GET['page'] == '340b_breaking_news' ): ?>
            <div class="form-fields">
                <h3 class="adv-headings">Newsletter Type: </h3><br><br>
                <select name="newsletter_type" name="newsletter_type" class="newsletter_type" required>
                    <option value="none">None</option>
                    <option value="Breaking News">Breaking News</option>
                    <option value="News Alert">News Alert</option>
                    <option value="News Analysis">News Analysis</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="form-fields">
                <h3 class="adv-headings">Newsletter Subject: </h3><br><br>
                <?php $news_subject = get_option('340_mailchimp_subject'); ?>
                <?php if( $_GET['page'] == '340b_breaking_news' ): ?>
                    <input type="text" name="340_mailchimp_subject" placeholder="Enter email subject" value="<?php echo ($news_subject) ? $news_subject : ''; ?>">
                    <?php else: ?>
                    <input type="text" name="340_mailchimp_subject" placeholder="Enter email subject" value="" required>
                <?php endif; ?>
            </div>
            <div class="form-fields">
                <h3 class="adv-headings">Preview Text: </h3><br><br>
                <?php $news_subject = get_option('340_mailchimp_preview_text'); ?>
                <textarea type="text" name="340_mailchimp_preview_text"><?php echo ($preview_text) ? $preview_text : ''; ?></textarea>
            </div>
            <div class="form-fields">
                <h3 class="adv-headings">Select posts to send in NewsLetter: </h3><br><br>
                <p class="error" style="color:red;display:none;font-size:15px;">No posts selected.</p>
                <select name="newsletter_posts" name="states[]" multiple="multiple" class="newsletter_posts">
                    <?php while ($all_posts->have_posts()) : $all_posts->the_post(); ?>
                        <option value="<?php the_ID(); ?>"><?php echo get_the_title(); ?></option>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </select>
            </div>
            <div class="form-fields">
                <h3 class="adv-headings">Select Ad to insert in the Header of Newsletter: </h3><br>
                <p class="note">If no Company is selected, a random Ad will be inserted in the Newsletter</p><br><br>
                <select name="newsletter_top_ad" name="newsletter_top_ad" class="newsletter_top_ad">
                <option value="0">Select name of the Company</option>
                    <?php foreach( $two_ads as $com ): ?>
                        <option value="<?php echo $com->id; ?>"><?php echo json_decode($com->company_data)->company_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if( $_GET['page'] != '340b_breaking_news' ): ?>
            <div class="form-fields">
                <h3 class="adv-headings">Select Ad to insert in middle of Newsletter: </h3><br>
                <p class="note">If no Company is selected, a random Ad will be inserted in the Newsletter</p><br><br>
                <select name="newsletter_middle_ad" name="newsletter_middle_ad" class="newsletter_middle_ad">
                <option value="0">Select name of the Company</option>
                    <?php foreach( $middle as $com ): ?>
                        <option value="<?php echo $com->id; ?>"><?php echo json_decode($com->company_data)->company_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="form-fields">
                <h3 class="adv-headings">Select Ad to insert in the Footer of Newsletter: </h3><br>
                <p class="note">If no Company is selected, a random Ad will be inserted in the Newsletter</p><br><br>
                <select name="newsletter_bottom_ad" name="newsletter_bottom_ad" class="newsletter_bottom_ad">
                <option value="0">Select name of the Company</option>
                    <?php foreach( $two_ads as $com ): ?>
                        <option value="<?php echo $com->id; ?>"><?php echo json_decode($com->company_data)->company_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-adver additional-article">
                <div class="form-fields">
                    <h3 class="adv-headings">Additional Notes by publisher </h3>
                </div>
                <div class="form-fields">
                <?php
                    $id = "article_description_1";
                    $name = 'article_description_1';
                    $content = '';
                    $settings = array('tinymce' => true, 'textarea_name' => $name);
                    wp_editor($content, $id, $settings);
                ?>
                </div>
            </div>
            <div class="form-adver add-wrap">
                <div class="form-fields"><a href="javascript:void(0);" class="add-more-post"><span class="plus"></span>Add another note</a></div>
            </div>
            <div class="form-adver additional-article" style="display:none;">
                <div class="form-fields">
                <?php
                    $id = "article_description_2";
                    $name = 'article_description_2';
                    $content = '';
                    $settings = array('tinymce' => true, 'textarea_name' => $name);
                    wp_editor($content, $id, $settings);
                ?>
                </div>
                <div class="form-fields"> <a href="javascript:void(0);" class="remove-above-post"><span class="minus"></span>Remove the above note</a></div>
            </div>
            <?php if( $_GET['page'] != '340b_breaking_news' ): ?>
            <div class="form-adver spotlight">
                <div class="form-fields">
                    <h3 class="adv-headings">Spotlight Section (Optional) </h3>
                </div>
                <div class="form-fields"><input name="spotlight_title" type="text" placeholder="Name, Title, Company"></div>
                <div class="form-fields">
                    <div class="image">
                            <img width="300" src="" class="image" style="margin-top:10px;" />
                            <input type="hidden" name="upload_spotlight" class="wp_attachment_id" />
                    </div>
                    <div class="btn">
                        <input type="button" value="Choose Headshot Photo" class="button-primary" id="spotlight" />
                    </div>
                </div>
                <div class="form-fields">
                    <h3 class="adv-headings">Select Spotlight to send in NewsLetter: </h3><br><br>
                    <select name="newsletter_spotlights" name="spotlights" class="newsletter_spotlights">
                        <?php while ($all_spotlights->have_posts()) : $all_spotlights->the_post(); ?>
                            <option value="<?php the_ID(); ?>"><?php echo get_the_title(); ?></option>
                        <?php endwhile;
                        wp_reset_postdata(); ?>
                    </select>
                </div>
            </div>
            <div class="form-adver provider_340b">
                <div class="form-fields">
                    <h3 class="adv-headings">340B Provider Section (Optional) </h3>
                </div>
                <div class="form-fields"><input name="provider_title" type="text" placeholder="Name, Title, Company"></div>
                <div class="form-fields">
                    <div class="image">
                            <img width="300" src="" class="image" style="margin-top:10px;" />
                            <input type="hidden" name="upload_provider" class="wp_attachment_id" />
                    </div>
                    <div class="btn">
                        <input type="button" value="Choose Headshot Photo" class="button-primary" id="provider_340b" />
                    </div>
                </div>
                <div class="form-fields">
                    <h3 class="adv-headings">Select provider to send in NewsLetter: </h3><br><br>
                    <select name="newsletter_providers" name="providers" class="newsletter_providers">
                        <?php while ($all_providers->have_posts()) : $all_providers->the_post(); ?>
                            <option value="<?php the_ID(); ?>"><?php echo get_the_title(); ?></option>
                        <?php endwhile;
                        wp_reset_postdata(); ?>
                    </select>
                </div>
            </div>
            <div class="form-adver marketing_graphic">
                <div class="form-fields">
                    <h3 class="adv-headings">Marketing Graphic</h3>
                </div>
                <div class="form-fields">
                    <h3 class="adv-headings">CTA Text</h3>
                    <input name="marketing_graphic_text" type="text" placeholder="Text"></div>
                <div class="form-fields">
                    <h3 class="adv-headings">CTA URL</h3>    
                    <input name="marketing_graphic_url" type="text" placeholder="URL"></div>
                <div class="form-fields">
                    <h3 class="adv-headings">URL for Image</h3>    
                    <input name="marketing_graphic_url_for_image" type="text" placeholder="URL for Image"></div>
                <div class="form-fields">
                    <div class="image">
                            <img width="300" src="" class="image" style="margin-top:10px;" />
                            <input type="hidden" name="marketing_graphic_image" class="wp_attachment_id" />
                    </div>
                    <div class="btn">
                        <input type="button" value="Choose Photo" class="button-primary" id="marketing_graphic" />
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-fields closing_message_members">
                <h3 class="adv-headings">Closing Message: </h3><br><br>
                <!-- <textarea name="closing_message"></textarea> -->
                <?php
                    $id = "closing_message_members";
                    $name = 'closing_message_members';
                    $content = str_replace('\\', '',$closing_message_members);
                    $settings = array('tinymce' => true, 'textarea_name' => $name);
                    wp_editor($content, $id, $settings);
                ?>
            </div>
            <div class="form-fields closing_message_subscribers">
                <h3 class="adv-headings">Closing Message: </h3><br><br>
                <!-- <textarea name="closing_message"></textarea> -->
                <?php
                    $id = "closing_message_subscribers";
                    $name = 'closing_message_subscribers';
                    $content = str_replace('\\', '', $closing_message_subscribers);
                    $settings = array('tinymce' => true, 'textarea_name' => $name);
                    wp_editor($content, $id, $settings);
                ?>
            </div>
            <div class="form-fields not-visible">
                <input type="hidden" name="action" value="340b_mailchimp_newsletter" />
                <?php $mailchimp_340b_nonce = wp_create_nonce('mailchimp_340b_nonce');  ?>
                <input type="hidden" name="mailchimp_340b_nonce" value="<?php echo $mailchimp_340b_nonce; ?>" />
            </div>
            <input type="submit" value="Create Newsletter" class="button-primary create-newsletter" style="margin-top: 20px;">
        </div>
        <div class="advertise-form">
            <div class="center-form">
                <?php if ($newsletters_in_draft) : ?>
                    <div class="form-fields">
                        <h3 class="adv-headings">Newsletter in Draft: </h3><br><br>
                        <h4><span style="font-size:1.3em;font-weight:400;">Subject:</span> <?php echo get_option('340_mailchimp_subject'); ?></h4>
                        <h4><span style="font-size:1.3em;font-weight:400;">Preview Text:</span> <?php echo get_option('340_mailchimp_preview_text'); ?></h4>
                        <?php if( $audience != "" ): ?>
                        <h4><span style="font-size:1.3em;font-weight:400;">Audience:</span> <?php echo ($audience == "3366313" ) ? 'Not on a Plan in Memberful' : ( ($audience == "3374743" ) ? 'Paid Subscribers': ''); ?></h4>
                        <?php endif; ?>
                    </div>
                    <div class="form-fields">
                        <h4><span style="font-size:1.3em;font-weight:400;">Selected Posts:</span></h4>
                        <?php if($_GET['page']== '340b_breaking_news'){ 
                        $drafts = json_decode(get_option('340_mailchimp_breaking_news_posts'));
                    }else{
                    	$drafts = json_decode(get_option('340_mailchimp_newsletter_posts')); 
                    }?>
                        <?php $additional = json_decode(get_option('340_mailchimp_articles')); ?>
                        <ol>
                        <?php if( !empty($additional[0]) ): ?>
                            <li><?php echo $additional[0]->copy; ?> <strong>( Additional )</strong></li>
                        <?php endif; ?>
                            <?php foreach ($drafts as $p) : ?>
                                <li><?php echo get_the_title($p); ?></li>
                            <?php endforeach; ?>
                            <?php if( !empty($additional[1]) ): ?>
                                <li><?php echo $additional[1]->copy; ?> <strong>( Additional )</strong></li>
                            <?php endif; ?>
                        </ol>
                    </div>
                    <?php if( !empty($spotlight) ): ?>
                    <div class="form-fields">
                        <h3 class="adv-headings">Spotlight: </h3><br><br>
                        <h4><span style="font-size:1.3em;font-weight:400;">Title:</span> <?php echo $spotlight->title; ?></h4>
                        <h4><span style="font-size:1.3em;font-weight:400;">Image:</span></h4>
                        <img src="<?php echo wp_get_attachment_image_url($spotlight->image); ?>" alt="">
                        <h4><span style="font-size:1.3em;font-weight:400;">Selected Spotlight:</span> <?php echo get_the_title ($spotlight->post); ?></h4>
                    </div>
                    <?php endif; ?>
                    <?php if( !empty($provider) ): ?>
                    <div class="form-fields">
                        <h3 class="adv-headings">Provider: </h3><br><br>
                        <h4><span style="font-size:1.3em;font-weight:400;">Title:</span> <?php echo $provider->title; ?></h4>
                        <h4><span style="font-size:1.3em;font-weight:400;">Image:</span></h4>
                        <img src="<?php echo wp_get_attachment_image_url($provider->image); ?>" alt="">
                        <h4><span style="font-size:1.3em;font-weight:400;">Selected Provider:</span> <?php echo get_the_title ($provider->post); ?></h4>
                    </div>
                    <?php endif; ?>
                    <?php if( !empty($marketing_graphic) ): ?>
                    <div class="form-fields">
                        <h3 class="adv-headings">Marketing Graphic: </h3><br><br>
                        <h4><span style="font-size:1.3em;font-weight:400;">CTA text:</span> <?php echo $marketing_graphic->text; ?></h4>
                        <h4><span style="font-size:1.3em;font-weight:400;">CTA URL:</span> <?php echo $marketing_graphic->url; ?></h4>
                        <h4><span style="font-size:1.3em;font-weight:400;">URL for Image:</span> <?php echo $marketing_graphic->url_for_image; ?></h4>
                        <h4><span style="font-size:1.3em;font-weight:400;">Image:</span></h4>
                        <img src="<?php echo wp_get_attachment_image_url($marketing_graphic->image); ?>" alt="">
                    </div>
                    <?php endif; ?>
                    <div class="form-fields">
                    <h4><span style="font-size:1.3em;font-weight:400;">Closing message:</span> </h4>
                    <div class="copy">
                    <?php echo ($audience == "3366313" ) ? $closing_message_subscribers : ( ($audience == "3374743" ) ? $closing_message_members : ''); ?>
                    </div>
                    </div>
                    <div class="form-fields not-visible">
                        <input type="hidden" name="action" value="340b_mailchimp_newsletter_send" />
                        <?php $mailchimp_340b_send_nonce = wp_create_nonce('mailchimp_340b_send_nonce');  ?>
                        <input type="hidden" name="mailchimp_340b_send_nonce" value="<?php echo $mailchimp_340b_send_nonce; ?>" />
                    </div>
                    <input type="submit" value="Send Newsletter" class="button-primary send-newsletter" style="margin-top: 20px;">
                 
                    <input type="button" value="Remove Draft" class="button-secondary remove-draft" style="margin-top: 20px;margin-left:20px;" data-nonce="<?php echo wp_create_nonce('340b_remove_mailchimp_drafts_nonce'); ?>" data-action="340b_remove_mailchimp_drafts">
                  
                <?php else : ?>
                    <div class="form-fields">
                        <h3 class="adv-headings">No Newsletters in Draft. </h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    $(document).on('ready', function() {
        $('.340b-newsletter-posts .newsletter_posts').select2({
            placeholder: "Select / Type the post name to send",
        });
        $('.340b-newsletter-posts .newsletter_spotlights').select2({
            placeholder: "Select Spotlight to send in NewsLetter"
        });
    });
</script>