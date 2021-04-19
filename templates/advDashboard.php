<div class="ad-dashboard wrap">
    <h1 class="wp-heading-inline">Advertisements</h1>
    <table id="adv-dashboard">
        <thead>
            <tr role="row">
                <th>Sr No.</th>
                <th>Publisher</th>
                <th>Company Name</th>
                <th>Membership Type</th>
                <th>Status</th>
                <th>Edit Membership</th>
                <th>Edit Data</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($result as $row) : ?>
                <tr role="row" class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
                    <td class=" dt-body-center"><?php echo $i; ?></td>
                    <td class=" dt-body-center"><?php echo $row->user_nicename; ?></td>
                    <td class=" dt-body-center"><?php echo json_decode($row->company_data)->company_name; ?></td>
                    <td class=" dt-body-center"><?php echo ($row->membership_type == "") ? 'Please select Membership' : ucwords(str_replace('_', ' ',$row->membership_type)); ?></td>
                    <td class=" dt-body-center" style="color:<?php echo ($row->status) ? 'green' : 'red'; ?>"><b><?php echo ($row->status) ? 'Active' : 'Inactive'; ?></b></td>
                    <td class=" dt-body-center"><a href="/wp-admin/admin.php?page=advertisers_dashboard&ad_type=edit&advert_id=<?php echo $row->id; ?>" class="page-title-action ad-new-ad">Edit</a></td>
                    <td class=" dt-body-center"><a href="/wp-admin/admin.php?page=advertisers_dashboard&ad_type=edit_data&user_id=<?php echo $row->user_id; ?>" class="page-title-action ad-new-ad">Modify</a></td>
                </tr>
            <?php $i++; endforeach; ?>
        </tbody>
        <tfoot>
            <tr role="row">
                <th>Sr No.</th>
                <th>Publisher</th>
                <th>Company Name</th>
                <th>Membership Type</th>
                <th>Status</th>
                <th>Edit Membership</th>
                <th>Edit Data</th>
            </tr>
        </tfoot>
    </table>
</div>