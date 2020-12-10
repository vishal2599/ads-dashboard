<?php
$mysql = 'SELECT ' . $wpdb->prefix . 'advertisements_dash.*, ' . $wpdb->prefix . 'users.user_nicename FROM ' . $wpdb->prefix . 'advertisements_dash INNER JOIN ' . $wpdb->prefix . 'users ON ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $wpdb->prefix . 'users.ID WHERE ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $user->ID;

$data = $wpdb->get_row($mysql);

?>
<div class="adv-create-ad wrap">
    <h1 class="wp-heading-inline">Manage Your Ads</h1>
    <h2 class="wp-heading-inline">Your Plan Level: <a href="javascript:void(0);"><?php echo ucwords(str_replace('_', ' ', $data->membership_type)); ?></a></h1>
    <form class="adv-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name='adv_dashboard_form' enctype='multipart/form-data'>
        <?php if (isset($data->membership_type) && $data->membership_type != 'emerald') : ?>
        <div class="advertise-add">
            <div class="form-fields banner">
                <h3>Banner Ad: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->banner_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_banner" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->banner_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="banner" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='banner_url' <?php echo (!empty($data)) ? 'value=' . $data->banner_url : ''; ?>>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-fields in_story">
                <h3>In-Article Ad: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->in_story_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_in_story" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->in_story_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="in_story" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='in_story_url' <?php echo (!empty($data)) ? 'value=' . $data->in_story_url : ''; ?>>
                </div>
            </div>
            <div class="form-fields footer">
                <h3>Footer Ad: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->footer_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_footer" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->footer_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="footer" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='footer_url' <?php echo (!empty($data)) ? 'value=' . $data->footer_url : ''; ?>>
                </div>
            </div>
            <div class="form-fields sidebar_one">
                <h3>Sidebar Ad 1: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->sidebar_one_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_sidebar_one" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->sidebar_one_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="sidebar_one" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='sidebar_one_url' <?php echo (!empty($data)) ? 'value=' . $data->sidebar_one_url : ''; ?>>
                </div>
            </div>
            <div class="form-fields sidebar_two">
                <h3>Sidebar Ad 2: </h3>
                <div class="image">
                    <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->sidebar_two_id) : ''; ?>" class="image" style="margin-top:10px;" />
                    <input type="hidden" name="upload_adv_sidebar_two" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->sidebar_two_id : ''; ?>" />
                </div>
                <div class="btn">
                    <input type="button" value="Upload/Change" class="button-primary" id="sidebar_two" />
                </div>
                <div class="url">
                    <input type='text' placeholder="Affiliate URL" name='sidebar_two_url' <?php echo (!empty($data)) ? 'value=' . $data->sidebar_two_url : ''; ?>>
                </div>
            </div>
            <div class="form-fields-not-visible">
                <input type="hidden" name="action" value="<?php echo (!empty($data)) ? 'edit_advert' : 'new_advert'; ?>" />
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
                    <div class="form-fields"><input type="text" placeholder="Company name as it will appear in the Experts Directory"></div>
                    <div class="form-fields"><input type="text" placeholder="Company website URL"></div>
                    <div class="form-fields"><textarea placeholder="Please limit to one paragraph"></textarea></div>
                </div>
                <div class="form-adver">
                    <h4>Upcoming Events:</h4>
                    <div class="form-fields"><input type="text" placeholder="Date of Event"></div>
                    <div class="form-fields"><input type="text" placeholder="Title of Event"></div>
                    <div class="form-fields"><textarea placeholder="Event Description" spellcheck="false"></textarea></div>
                </div>
                <div class="form-adver">
                    <div class="form-fields"><button class="add-more"><span class="plus"></span>Add Another Events</button></div>
                </div>
            </div>
        </div>
    </form>
</div>