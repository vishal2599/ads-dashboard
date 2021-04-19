<div class="adv-create-ad wrap">
    <h1 class="wp-heading-inline">Edit the Advertisement</h1>
        <a href="/wp-admin/admin.php?page=advertisers_dashboard" class="page-title-action ad-new-ad">
            <- Back to Ads Dashboard</a>
            <form class="adv_admin_edit" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name='adv_dashboard_edit_form' enctype='multipart/form-data'>
                <?php
                $mysql = 'SELECT ' . $wpdb->prefix . 'advertisements_dash.*, ' . $wpdb->prefix . 'users.user_nicename FROM ' . $wpdb->prefix . 'advertisements_dash INNER JOIN ' . $wpdb->prefix . 'users ON ' . $wpdb->prefix . 'advertisements_dash.user_id = ' . $wpdb->prefix . 'users.ID WHERE ' . $wpdb->prefix . 'advertisements_dash.id = ' . $_GET['advert_id'];

                $data = $wpdb->get_row($mysql);
                ?>
                <div class="form-fields">
                    <h3 class="adv-headings">Advertiser:</h3>
                    <?php $usr_data = get_userdata($data->user_id); ?>
                    <p><?php echo $usr_data->data->display_name; ?></p>
                </div>
                <div class="form-fields">
                    <h3 class="adv-headings">Membership Type: </h3>
                    <select name="membership_type">
                        <option value="" <?php echo ($data->membership_type == '') ? 'selected': ''; ?>>Select Membership</option>
                        <option value="emerald" <?php echo ($data->membership_type == 'emerald') ? 'selected': ''; ?>>Emerald</option>
                        <option value="diamond" <?php echo ($data->membership_type == 'diamond') ? 'selected': ''; ?>>Diamond</option>
                        <option value="ultra_diamond" <?php echo ($data->membership_type == 'ultra_diamond') ? 'selected': ''; ?>>Ultra Diamond</option>
                    </select>
                </div>
                <div class="form-fields">
                        <h3 class="adv-headings">Status</h3>
                        <input type="radio" name="status" value="0" <?php echo (!empty($data)) ? ($data->status == false ? 'checked' : '') : ''; ?>> <span style="color:red;">Inactive</span>
                        <input type="radio" name="status" value="1" <?php echo (!empty($data)) ? ($data->status > 0 ? 'checked' : '') : ''; ?>> <span style="color:green;">Active</span>
                </div>
                <div class="form-fields not-visible">
                    <input type="hidden" name="action" value="admin_edit_advert" />
                    <input type="hidden" name="advert_id" value="<?php echo $_GET['advert_id']; ?>" />
                    <?php $edit_advertisement_data_nonce = wp_create_nonce('edit_advertisement_data_nonce');  ?>
                    <input type="hidden" name="edit_advertisement_data_nonce" value="<?php echo $edit_advertisement_data_nonce; ?>" />
                </div>
                <input type="submit" value="Submit" class="button-primary" style="margin-top: 20px;">
                </form>
</div>