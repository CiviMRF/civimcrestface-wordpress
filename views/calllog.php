<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */
?>
<div class="wrap">

<h1 class="wp-heading-inline"><?php esc_html_e( 'CiviCRM McRestFace Call Log' , 'wpcmrf');?></h1>
<a href="<?php echo self_admin_url( 'options-general.php?page=wpcmrf_calllog&action=clear'); ?>" class="page-title-action">Clear</a>


<table class="wp-list-table widefat fixed striped wpcmrf_profiles">
    <thead>
        <tr>
            <th><?php esc_html_e( 'ID' , 'wpcmrf');?></th>
            <th><?php esc_html_e( 'Connector ID' , 'wpcmrf');?></th>
            <th><?php esc_html_e( 'Date' , 'wpcmrf');?></th>
            <th><?php esc_html_e( 'Status' , 'wpcmrf');?></th>
            <th><?php esc_html_e( 'Request' , 'wpcmrf');?></th>
            <th><?php esc_html_e( 'Reply' , 'wpcmrf');?></th>
            <th><?php esc_html_e( 'Cached till' , 'wpcmrf');?></th>
        </tr>
    </thead>
    <?php foreach($calls as $call) { ?>
        <tr>
            <td><?php echo $call->cid; ?></td>
            <td><?php echo $call->connector_id; ?></td>
            <td><?php echo $call->create_date; ?></td>
            <td><?php echo $call->status; ?></td>
            <td><pre><?php echo json_encode(json_decode($call->request, true), JSON_PRETTY_PRINT); ?></pre></td>
            <td><pre><?php echo json_encode(json_decode($call->reply, true), JSON_PRETTY_PRINT); ?></pre></td>
            <td><?php echo $call->cached_until; ?></td>
        </tr>
    <?php } ?>
</table>

</div>