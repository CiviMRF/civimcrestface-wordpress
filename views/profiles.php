<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */
?>
<div class="wrap">

<h1 class="wp-heading-inline"><?php esc_html_e( 'CiviCRM McRestFace Profiles' , 'wpcmrf');?></h1>
<a href="<?php echo self_admin_url( 'options-general.php?page=wpcmrf_admin&action=new'); ?>" class="page-title-action">Add New</a>


<table class="wp-list-table widefat fixed striped wpcmrf_profiles">
    <thead>
        <tr>
            <th><?php esc_html_e( 'ID' , 'wpcmrf');?></th>
            <th><?php esc_html_e( 'Title' , 'wpcmrf');?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <?php foreach($profiles as $profile) { ?>
        <tr>
            <td><?php echo $profile->id; ?></td>
            <td><?php echo $profile->label; ?></td>
            <td>
                <a href="<?php echo self_admin_url( 'options-general.php?page=wpcmrf_admin&action=edit&profile_id=' . $profile->id); ?>"><?php esc_html_e( 'Edit' , 'wpcmrf');?></a>&nbsp;
                <a href="<?php echo self_admin_url( 'options-general.php?page=wpcmrf_admin&action=delete&profile_id=' . $profile->id); ?>"><?php esc_html_e( 'Delete' , 'wpcmrf');?></a></td>
        </tr>
    <?php } ?>
</table>

</div>