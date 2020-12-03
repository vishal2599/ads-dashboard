<div class="ad-dashboard wrap">
    <h1 class="wp-heading-inline">Advertisements</h1>
    <?php if (in_array('advertiser', (array) $user->roles) || in_array('administrator', (array) $user->roles)) : ?>
        <a href="/wp-admin/admin.php?page=advertisers_dashboard&ad_type=new" class="page-title-action ad-new-ad">Add New</a>
    <?php endif; ?>
    <table id="adv-dashboard">
        <thead>
            <tr role="row">
                <th>ID</th>
                <?php if (in_array('administrator', (array) $user->roles)) : ?>
                    <th>Publisher</th>
                <?php endif; ?>
                <th>Banner</th>
                <th>URL</th>
                <!-- <th>Shortcode</th> -->
                <th>Status</th>
                <?php if (in_array('administrator', (array) $user->roles)) : ?>
                    <th>Edit</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($result as $row) : ?>
                <tr role="row" class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
                    <td class=" dt-body-center"><?php echo $row->id; ?></td>
                    <?php if (in_array('administrator', (array) $user->roles)) : ?>
                        <td class=" dt-body-center"><?php echo $row->user_nicename; ?></td>
                    <?php endif; ?>
                    <td class=" dt-body-center"><img src="<?php echo wp_get_attachment_url($row->banner_id); ?>" width="100"></td>
                    <td class=" dt-body-center"><?php echo $row->affiliate; ?></td>
                    <!-- <td class=" dt-body-center"><?php //echo '[affiliate_adv number="' . $row->id . '"]'; ?></td> -->
                    <td class=" dt-body-center" style="color:<?php echo ($row->status) ? 'green' : 'red'; ?>"><b><?php echo ($row->status) ? 'Active' : 'Inactive'; ?></b></td>
                    <?php if (in_array('administrator', (array) $user->roles)) : ?>
                        <td class=" dt-body-center"><a href="/wp-admin/admin.php?page=advertisers_dashboard&ad_type=edit&advert_id=<?php echo $row->id; ?>" class="page-title-action ad-new-ad">Edit</a></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr role="row">
                <th>ID</th>
                <?php if (in_array('administrator', (array) $user->roles)) : ?>
                    <th>Publisher</th>
                <?php endif; ?>
                <th>Banner</th>
                <th>URL</th>
                <!-- <th>Shortcode</th> -->
                <th>Status</th>
                <?php if (in_array('administrator', (array) $user->roles)) : ?>
                    <th>Edit</th>
                <?php endif; ?>
            </tr>
        </tfoot>
    </table>
</div>