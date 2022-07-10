<div class="ad-dashboard wrap">
    <h1 class="wp-heading-inline">Expert Directory Categories</h1>
    <table id="adv-dashboard">
        <thead>
            <tr role="row">
                <th>Sr No.</th>
                <th>Category Name</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($result as $row) : ?>
                <tr role="row" class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
                    <td class=" dt-body-center"><?php echo $i; ?></td>
                    <td class=" dt-body-center"><?php echo $row->category_name; ?></td>
                    <td class=" dt-body-center"><a href="/wp-admin/admin.php?page=adv_experts_directory&ad_type=edit&expert_id=<?php echo $row->id; ?>" class="page-title-action ad-new-ad">Edit</a></td>
                    <td class=" dt-body-center">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name='adv_delete_expert_category' enctype='multipart/form-data'>
                            <input type="hidden" name="exp_category_id" value="<?php echo $row->id; ?>">
                            <input type="hidden" name="action" value="delete_expCat" />

                            <?php $expert_category_delete_nonce = wp_create_nonce('expert_category_delete_nonce');  ?>
                            <input type="hidden" name="expert_category_delete_nonce" value="<?php echo $expert_category_delete_nonce; ?>" />

                            <input type="submit" value="Delete" class="page-title-action">
                        </form>
                    </td>
                </tr>
            <?php $i++;
            endforeach; ?>
        </tbody>
        <tfoot>
            <tr role="row">
                <th>Sr No.</th>
                <th>Category Name</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="adv-create-ad wrap">
    <h1 class="wp-heading-inline">Add a New Category</h1>
    <form class="adv-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name='adv_expert_category' enctype='multipart/form-data'>
        <input type="text" name="exp_category" placeholder="Category Name" style="margin-top: 20px;">
        <input type="hidden" name="action" value="create_expCat" />

        <?php $expert_category_create_nonce = wp_create_nonce('expert_category_create_nonce');  ?>
        <input type="hidden" name="expert_category_create_nonce" value="<?php echo $expert_category_create_nonce; ?>" />

        <input type="submit" value="Submit" class="button-primary" style="margin: 20px 0 0 20px;">
    </form>
</div>