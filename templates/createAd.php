<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo (isset($_GET['advert_id'])) ? 'Edit' : 'Add    '; ?> Details of the Advertisement</h1>
    <a href="/wp-admin/admin.php?page=advertisers_dashboard" class="page-title-action ad-new-ad"><- Back to Ads Dashboard</a> 
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name='adv_dashboard_form' enctype='multipart/form-data'>
            <?php
            if ($_GET['ad_type'] == 'edit') :
                $mysql = 'SELECT ' . $wpdb->prefix . 'advertisements_dash.*, ' . $wpdb->prefix . 'users.user_nicename FROM ' . $wpdb->prefix . 'advertisements_dash INNER JOIN ' . $wpdb->prefix . 'users ON ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $wpdb->prefix . 'users.ID WHERE ' . $wpdb->prefix . 'advertisements_dash.id = ' . (int)$_GET['advert_id'];

                $data = $wpdb->get_row($mysql);
            endif;

            ?>
            <div class="form-fields">
                <h3 class="adv-headings">Affiliate Link</h3>
                <input type='text' name='affiliate_url' <?php echo (isset($_GET['advert_id'])) ? 'value=' . $data->affiliate : ''; ?>>
            </div>
            <div class="banner" style="margin-top:20px;">
                <img width="300" src="<?php echo (isset($_GET['advert_id'])) ? wp_get_attachment_url($data->banner_id) : ''; ?>" class="image" style="margin-top:10px;<?php echo (isset($_GET['advert_id'])) ? '' : 'display:none;'; ?>" />
                <input type="button" value="Upload Banner" class="button-primary" id="upload_adv_banner" />
                <input type="hidden" name="upload_adv_banner" class="wp_attachment_id" value="<?php echo (isset($_GET['advert_id'])) ?  $data->banner_id : ''; ?>" /> </br>
            </div>
            <div class="in_story" style="margin-top:20px;">
                <img width="300" src="<?php echo (isset($_GET['advert_id'])) ? wp_get_attachment_url($data->in_story_id) : ''; ?>" class="image" style="margin-top:10px;<?php echo (isset($_GET['advert_id'])) ? '' : 'display:none;'; ?>" />
                <input type="button" value="Upload In-story" class="button-primary" id="upload_adv_in_story" />
                <input type="hidden" name="upload_adv_in_story" class="wp_attachment_id" value="<?php echo (isset($_GET['advert_id'])) ?  $data->in_story_id : ''; ?>" /> </br>
            </div>
            <div class="footer" style="margin-top:20px;">
                <img width="300" src="<?php echo (isset($_GET['advert_id'])) ? wp_get_attachment_url($data->footer_id) : ''; ?>" class="image" style="margin-top:10px;<?php echo (isset($_GET['advert_id'])) ? '' : 'display:none;'; ?>" />
                <input type="button" value="Upload Footer" class="button-primary" id="upload_adv_footer" />
                <input type="hidden" name="upload_adv_footer" class="wp_attachment_id" value="<?php echo (isset($_GET['advert_id'])) ?  $data->footer_id : ''; ?>" /> </br>
            </div>
            <div class="sidebar_one" style="margin-top:20px;">
                <img width="300" src="<?php echo (isset($_GET['advert_id'])) ? wp_get_attachment_url($data->sidebar_one_id) : ''; ?>" class="image" style="margin-top:10px;<?php echo (isset($_GET['advert_id'])) ? '' : 'display:none;'; ?>" />
                <input type="button" value="Upload sidebar_one" class="button-primary" id="upload_adv_sidebar_one" />
                <input type="hidden" name="upload_adv_sidebar_one" class="wp_attachment_id" value="<?php echo (isset($_GET['advert_id'])) ?  $data->sidebar_one_id : ''; ?>" /> </br>
            </div>
            <div class="sidebar_two" style="margin-top:20px;">
                <img width="300" src="<?php echo (isset($_GET['advert_id'])) ? wp_get_attachment_url($data->sidebar_two_id) : ''; ?>" class="image" style="margin-top:10px;<?php echo (isset($_GET['advert_id'])) ? '' : 'display:none;'; ?>" />
                <input type="button" value="Upload sidebar_two" class="button-primary" id="upload_adv_sidebar_two" />
                <input type="hidden" name="upload_adv_sidebar_two" class="wp_attachment_id" value="<?php echo (isset($_GET['advert_id'])) ?  $data->sidebar_two_id : ''; ?>" /> </br>
            </div>
            <div class="form-fields">
                <?php if (in_array('administrator', (array) $user->roles)) : ?>
                    <h3 class="adv-headings">Status</h3>
                    <input type="radio" name="status" value="0" <?php echo (isset($_GET['advert_id'])) ? ($data->status == false ? 'checked' : '') : ''; ?>> <span style="color:red;">Inactive</span>
                    <input type="radio" name="status" value="1" <?php echo (isset($_GET['advert_id'])) ? ($data->status > 0 ? 'checked' : '') : ''; ?>> <span style="color:green;">Active</span>
                <?php endif; ?>
            </div>
            <div class="form-fields not-visible">
                <?php if ($_GET['ad_type'] == 'edit') : ?>
                    <input type="hidden" name="action" value="edit_advert" />
                    <input type="hidden" name="advert_id" value="<?php echo $_GET['advert_id']; ?>" />
                <?php else : ?>
                    <input type="hidden" name="action" value="new_advert" />
                <?php endif; ?>
                <?php $new_advert_nonce = wp_create_nonce('new_advertisement_nonce');  ?>
                <input type="hidden" name="new_advert_nonce" value="<?php echo $new_advert_nonce ?>" />
            </div>
            <input type="submit" value="Submit" class="button-primary" style="margin-top: 20px;">
            </form>
</div>