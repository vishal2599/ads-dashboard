<div class="adv-create-ad wrap">
    <h1 class="wp-heading-inline">Add Details of the Advertisement</h1>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name='adv_dashboard_form' enctype='multipart/form-data'>
                <?php
                $mysql = 'SELECT ' . $wpdb->prefix . 'advertisements_dash.*, ' . $wpdb->prefix . 'users.user_nicename FROM ' . $wpdb->prefix . 'advertisements_dash INNER JOIN ' . $wpdb->prefix . 'users ON ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $wpdb->prefix . 'users.ID WHERE ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $user->ID;

                $data = $wpdb->get_row($mysql);

                ?>
                <div class="banner">
                    <div class="image">
                        <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->banner_id) : ''; ?>" class="image" style="margin-top:10px;" />
                        <input type="button" value="Select Banner" class="button-primary" id="upload_adv_banner" />
                        <input type="hidden" name="upload_adv_banner" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->banner_id : ''; ?>" />
                    </div>
                    <div class="url">
                        <input type='text' placeholder="Affiliate URL" name='banner_url' <?php echo (!empty($data)) ? 'value=' . $data->banner_url : ''; ?>>
                    </div>
                </div>
                <div class="in_story">
                    <div class="image">
                        <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->in_story_id) : ''; ?>" class="image" style="margin-top:10px;" />
                        <input type="button" value="Select In-story" class="button-primary" id="upload_adv_in_story" />
                        <input type="hidden" name="upload_adv_in_story" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->in_story_id : ''; ?>" />
                    </div>
                    <div class="url">
                        <input type='text' placeholder="Affiliate URL" name='in_story_url' <?php echo (!empty($data)) ? 'value=' . $data->in_story_url : ''; ?>>
                    </div>
                </div>
                <div class="footer">
                    <div class="image">
                        <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->footer_id) : ''; ?>" class="image" style="margin-top:10px;" />
                        <input type="button" value="Select Footer" class="button-primary" id="upload_adv_footer" />
                        <input type="hidden" name="upload_adv_footer" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->footer_id : ''; ?>" />
                    </div>
                    <div class="url">
                        <input type='text' placeholder="Affiliate URL" name='footer_url' <?php echo (!empty($data)) ? 'value=' . $data->footer_url : ''; ?>>
                    </div>
                </div>
                <div class="sidebar_one">
                    <div class="image">
                        <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->sidebar_one_id) : ''; ?>" class="image" style="margin-top:10px;" />
                        <input type="button" value="Select Sidebar 1" class="button-primary" id="upload_adv_sidebar_one" />
                        <input type="hidden" name="upload_adv_sidebar_one" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->sidebar_one_id : ''; ?>" />
                    </div>
                    <div class="url">
                        <input type='text' placeholder="Affiliate URL" name='sidebar_one_url' <?php echo (!empty($data)) ? 'value=' . $data->sidebar_one_url : ''; ?>>
                    </div>
                </div>
                <div class="sidebar_two">
                    <div class="image">
                        <img width="300" src="<?php echo (!empty($data)) ? wp_get_attachment_url($data->sidebar_two_id) : ''; ?>" class="image" style="margin-top:10px;" />
                        <input type="button" value="Select Sidebar 2" class="button-primary" id="upload_adv_sidebar_two" />
                        <input type="hidden" name="upload_adv_sidebar_two" class="wp_attachment_id" value="<?php echo (!empty($data)) ?  $data->sidebar_two_id : ''; ?>" />
                    </div>
                    <div class="url">
                        <input type='text' placeholder="Affiliate URL" name='sidebar_two_url' <?php echo (!empty($data)) ? 'value=' . $data->sidebar_two_url : ''; ?>>
                    </div>
                </div>
                <div class="form-fields not-visible">
                        <input type="hidden" name="action" value="<?php echo (!empty($data)) ? 'edit_advert' : 'new_advert'; ?>" />
                        <input type="hidden" name="advertiser_id" value="<?php echo $user->ID; ?>" />
                    <?php $new_advertisement_nonce = wp_create_nonce('new_advertisement_nonce');  ?>
                    <input type="hidden" name="new_advert_nonce" value="<?php echo $new_advertisement_nonce; ?>" />
                </div>
                <input type="submit" value="Submit" class="button-primary" style="margin-top: 20px;">
                </form>
</div>