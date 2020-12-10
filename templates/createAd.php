<?php
$mysql = 'SELECT ' . $wpdb->prefix . 'advertisements_dash.*, ' . $wpdb->prefix . 'users.user_nicename FROM ' . $wpdb->prefix . 'advertisements_dash INNER JOIN ' . $wpdb->prefix . 'users ON ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $wpdb->prefix . 'users.ID WHERE ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $user->ID;
$data = $wpdb->get_row($mysql);
if( !empty($data) ){
    $company_data = json_decode($data->company_data);
    $ad_data = json_decode($data->ad_data);
    $event_data = json_decode($data->event_data);
}

?>
<div class="adv-create-ad wrap">
    <h1 class="wp-heading-inline">Manage Your Ads</h1>
    <h2 class="wp-heading-inline">Your Plan Level: <a href="javascript:void(0);"><?php echo (isset($data->membership_type)) ? ucwords(str_replace('_', ' ', $data->membership_type)) : 'No Plan Selected'; ?></a></h1>
    <form class="adv-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name='adv_dashboard_form' enctype='multipart/form-data'>
        <div class="advertise-add">
        <?php if (isset($data->membership_type) && $data->membership_type != 'emerald') : ?>
            <div class="form-fields banner">
                <h3>Banner Ad: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($ad_data)) ? wp_get_attachment_url($ad_data->banner_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_banner" class="wp_attachment_id" value="<?php echo (!empty($ad_data)) ?  $ad_data->banner_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="banner" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='banner_url' <?php echo (!empty($ad_data)) ? 'value=' . $ad_data->banner_url : ''; ?> required>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-fields in_story">
                <h3>In-Article Ad: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($ad_data)) ? wp_get_attachment_url($ad_data->in_story_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_in_story" class="wp_attachment_id" value="<?php echo (!empty($ad_data)) ?  $ad_data->in_story_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="in_story" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='in_story_url' <?php echo (!empty($ad_data)) ? 'value=' . $ad_data->in_story_url : ''; ?> required>
                </div>
            </div>
            <div class="form-fields footer">
                <h3>Footer Ad: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($ad_data)) ? wp_get_attachment_url($ad_data->footer_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_footer" class="wp_attachment_id" value="<?php echo (!empty($ad_data)) ?  $ad_data->footer_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="footer" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='footer_url' <?php echo (!empty($ad_data)) ? 'value=' . $ad_data->footer_url : ''; ?> required>
                </div>
            </div>
            <div class="form-fields sidebar_one">
                <h3>Sidebar Ad 1: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($ad_data)) ? wp_get_attachment_url($ad_data->sidebar_one_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_sidebar_one" class="wp_attachment_id" value="<?php echo (!empty($ad_data)) ?  $ad_data->sidebar_one_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="sidebar_one" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='sidebar_one_url' <?php echo (!empty($ad_data)) ? 'value=' . $ad_data->sidebar_one_url : ''; ?> required>
                </div>
            </div>
            <div class="form-fields sidebar_two">
                <h3>Sidebar Ad 2: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($ad_data)) ? wp_get_attachment_url($ad_data->sidebar_two_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_sidebar_two" class="wp_attachment_id" value="<?php echo (!empty($ad_data)) ?  $ad_data->sidebar_two_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="sidebar_two" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='sidebar_two_url' <?php echo (!empty($ad_data)) ? 'value=' . $ad_data->sidebar_two_url : ''; ?> required>
                </div>
            </div>
            <div class="form-fields-not-visible">
                <input type="hidden" name="action" value="<?php echo (!empty($ad_data)) ? 'edit_advert' : 'new_advert'; ?>" />
                <input type="hidden" name="advertiser_id" value="<?php echo $user->ID; ?>" />
                <?php $new_advertisement_nonce = wp_create_nonce('new_advertisement_nonce');  ?>
                <input type="hidden" name="new_advert_nonce" value="<?php echo $new_advertisement_nonce; ?>" />
            </div>
            <input type="submit" value="Submit" class="button-primary" style="margin-top: 20px;">
        </div>
        <div class="advertise-form">
            <div class="center-form">
                <h3>Please add the following information for the advertiser to include:</h3>
                <div class="form-adver">
                    <h4>Company Profile:</h4>
                    <div class="form-fields"><input name="company_name" type="text" <?php echo (!empty($company_data)) ? 'value=' . $company_data->company_name : ''; ?> placeholder="Company name as it will appear in the Experts Directory"></div>
                    <div class="form-fields"><input name="company_url" type="text" <?php echo (!empty($company_data)) ? 'value=' . $company_data->company_url : ''; ?> placeholder="Company website URL"></div>
                    <div class="form-fields"><textarea name="company_description" placeholder="Please limit to one paragraph"><?php echo (!empty($company_data)) ? $company_data->company_description : ''; ?></textarea></div>
                </div>
                <?php if( !empty($event_data) ): $i = 0; ?>
                <?php foreach($event_data as $eve): ?>
                    <div class="form-adver upcoming-events">
                    <?php echo ($i == 0) ? '<h4>Upcoming Events:</h4>': ''; ?>
                    <div class="form-fields"><input name="event_date[]" type="date" placeholder="Date of Event" value="<?php echo $eve->event_date; ?>"></div>
                    <div class="form-fields"><input name="event_title[]" type="text" placeholder="Title of Event" value="<?php echo $eve->event_title; ?>"></div>
                    <div class="form-fields"><textarea name="event_description[]" placeholder="Event Description" spellcheck="false"><?php echo $eve->event_description; ?></textarea></div>
                </div>
                <?php $i++; endforeach; else: ?>
                <div class="form-adver upcoming-events">
                    <h4>Upcoming Events:</h4>
                    <div class="form-fields"><input name="event_date[]" type="date" placeholder="Date of Event"></div>
                    <div class="form-fields"><input name="event_title[]" type="text" placeholder="Title of Event"></div>
                    <div class="form-fields"><textarea name="event_description[]" placeholder="Event Description" spellcheck="false"></textarea></div>
                </div>
                <?php endif; ?>
                <div class="form-adver">
                    <div class="form-fields"><a href="javascript:void(0);" class="add-more"><span class="plus"></span>Add Another Event</a></div>
                </div>
            </div>
        </div>
    </form>
</div>