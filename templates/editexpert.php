<div class="adv-create-ad wrap">
    <h1 class="wp-heading-inline">Edit the Expert Category</h1>
        <a href="/wp-admin/admin.php?page=adv_experts_directory" class="page-title-action ad-new-ad">
            <- Back to Experts Directory</a>
            <form class="adv_admin_edit" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name='adv_dashboard_edit_form' enctype='multipart/form-data'>
                <?php
                $mysql = 'SELECT category_name FROM  '. $wpdb->prefix . 'adv_expert_categories '. 'WHERE ' . $wpdb->prefix . 'adv_expert_categories.id = ' . $_GET['expert_id'];
                //print_r(esc_url(admin_url('admin-post.php')));

                $data = $wpdb->get_row($mysql);
                //print_r($data);
                ?>
                <div class="form-fields">
                    <h3 class="adv-headings">Expert Category:</h3>
                </div>
                <div class="form-fields">
                    <h3 class="adv-headings">Category Name: </h3>
                    <input type="text" name="category" value="<?php echo $data->category_name;?>">

                </div>
               
                <div class="form-fields not-visible">
                    <input type="hidden" name="action" value="admin_edit_expert" />
                    <input type="hidden" name="expert_id" value="<?php echo $_GET['expert_id']; ?>" />
                    <?php $edit_expert_data_nonce = wp_create_nonce('edit_expert_data_nonce');  ?>
                    <input type="hidden" name="edit_expert_data_nonce" value="<?php echo $edit_expert_data_nonce; ?>" />
                </div>
                <input type="submit" value="Submit" class="button-primary" style="margin-top: 20px;">
                </form>
</div>