<?php

/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

?>
<div class="wrap">

<h1 class="wp-heading-inline"><?php esc_html_e('CiviCRM McRestFace Connection', 'wpcmrf');?></h1>

    <?php echo \CMRF\Wordpress\Admin\AdminPage::validate($profile->id ?? ''); ?>

    <form name="wpcmrf_admin" id="wpcmrf_admin" action="<?php echo self_admin_url('options-general.php?page=wpcmrf_admin&action=save&profile_id=' . ($profile->id ?? '')); ?>" method="POST">
        <div class="inside">
            <table cellspacing="0">
                <tbody>
                <tr>
                    <th width="20%" align="left" scope="row"><?php esc_html_e('Name', 'wpcmrf');?></th>
                    <td width="5%"/>
                    <td align="left">
                        <span><input id="label" name="label" type="text" size="15" value="<?php echo esc_attr($profile->label ?? ''); ?>" class="regular-text code"></span>
                    </td>
                </tr>
                <tr>
                    <th width="20%" align="left" scope="row"><?php esc_html_e('CiviCRM Connector', 'wpcmrf');?></th>
                    <td width="5%"/>
                    <td align="left">
                        <span>
                            <select id="connector" name="connector">
                                <?php foreach ($connectors as $connector_type => $connector) {?>
                                  <option value="<?php echo esc_attr($connector_type); ?>" <?php if (isset($profile) && $profile->connector == $connector_type) {
                                        ?>selected="selected"<?php
                                                 } ?>><?php echo esc_html($connector['label']); ?></option>
                                <?php } ?>
                            </select>
                        </span>
                    </td>
                </tr>
                <tr class="wpcmrf_remote">
                    <th width="20%" align="left" scope="row"><?php esc_html_e('CiviCRM Rest URL', 'wpcmrf');?></th>
                    <td width="5%"/>
                    <td align="left">
                        <span><input id="url" name="url" type="text" size="15" value="<?php echo esc_attr($profile->url ?? ''); ?>" class="regular-text code"></span>
                        <p class="description"><?php esc_html_e('E.g. https://my-civi.org/sites/all/modules/civicrm/extern/rest.php (Drupal 7)'); ?></p>
                        <p class="description"><?php esc_html_e('or https://my-civi.org/civicrm/ajax/rest (Drupal 8/9 using AuthX)'); ?></p>
                        <p class="description"><?php esc_html_e('or https://my-civi.org/wp-json/civicrm/v3/rest (WordPress)'); ?></p>
                    </td>
                </tr>
                <tr class="wpcmrf_remote">
                    <th width="20%" align="left" scope="row"><?php esc_html_e('CiviCRM Rest URL (v4)', 'wpcmrf');?></th>
                    <td width="5%"/>
                    <td align="left">
                        <span><input id="urlV4" name="urlV4" type="text" size="15" value="<?php echo esc_attr($profile->urlV4 ?? ''); ?>" class="regular-text code"></span>
                        <p class="description"><?php esc_html_e('E.g. https://my-civi.org/sites/all/modules/civicrm/extern/rest.php (Drupal 7)'); ?></p>
                        <p class="description"><?php esc_html_e('or https://my-civi.org/civicrm/ajax/api4 (Drupal 8/9 using AuthX)'); ?></p>
                        <p class="description"><?php esc_html_e('or https://my-civi.org/civicrm/ajax/api4 (WordPress)'); ?></p>
                    </td>
                </tr>

                <tr class="wpcmrf_remote">
                    <th width="20%" align="left" scope="row"><?php esc_html_e('CiviCRM Site Key', 'wpcmrf');?></th>
                    <td width="5%"/>
                    <td align="left">
                        <span><input id="site_key" name="site_key" type="text" size="15" value="<?php echo esc_attr($profile->site_key ?? ''); ?>" class="regular-text code"></span>
                    </td>
                </tr>

                <tr class="wpcmrf_remote">
                    <th width="20%" align="left" scope="row"><?php esc_html_e('CiviCRM API Key', 'wpcmrf');?></th>
                    <td width="5%"/>
                    <td align="left">
                        <span><input id="api_key" name="api_key" type="text" size="15" value="<?php echo esc_attr($profile->api_key ?? ''); ?>" class="regular-text code"></span>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <div id="major-publishing-actions">
          <?php wp_nonce_field('wpcmrf_admin') ?>
            <div id="publishing-action">
                <input type="hidden" name="profile_id" value="<?php echo esc_attr($profile->id ?? ''); ?>" />
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'wpcmrf');?>">
            </div>
            <div class="clear"></div>
        </div>
    </form>

    <script type="text/javascript">
      jQuery(document).ready(function($){
            $('#connector').on('change', function() {
                if ($(this).val() == 'local') {
                    $('.wpcmrf_remote').hide();
                } else {
                  $('.wpcmrf_remote').show();
                }
            });
            $('#connector').trigger('change');
        });
    </script>
</div>
