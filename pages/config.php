<?php
auth_reauthenticate();
access_ensure_global_level(config_get('manage_plugin_threshold'));

layout_page_header(plugin_lang_get('title'));
layout_page_begin();

print_manage_menu();

?>
<div class="col-md-12 col-xs-12">
    <div class="space-10"></div>
    <div class="form-container">
        <h1><p class="text-center"><?php echo plugin_lang_get( "title" ) ?></p></h1>
        <li>
            Get Instructions from <a target="_blank" rel="noopener" href="https://github.com/ugleiton/MantisAzureOauth">https://github.com/ugleiton/MantisAzureOauth</a>
        </li>
        <li>Use the following redirect URL in Azure<br>
            <code><?php echo substr(config_get('path'), 0, -1).plugin_page('redirect'); ?></code>
        </li>

        <form action="<?php echo plugin_page('config_update') ?>" method="post">
            <?php echo form_security_field('plugin_MantisAzureOauth_config_update'); ?>
            <table>
                <tr>
                    <td class="category col-sm-3 control-label label-info label-white"><?php echo plugin_lang_get('tenantId') ?></td>
                    <td><input type="text" class="form-control" name="prefTenantId" value="<?php echo plugin_config_get('tenantId') ?>"></td>
                </tr>
                <tr>
                    <td class="category col-sm-3 control-label label-info label-white"><?php echo plugin_lang_get('clientId') ?></td>
                    <td><input type="text" class="form-control" name="prefClientID" value="<?php echo plugin_config_get('clientId') ?>"></td>
                </tr>
                <tr>
                    <td class="category col-sm-3 control-label label-info label-white"><?php echo plugin_lang_get('clientSecret') ?></td>
                    <td><input type="text" class="form-control" name="prefClientSecret" value="<?php echo plugin_config_get('clientSecret') ?>"></td>
                </tr>
                <tr>
                    <td class="category col-sm-3 control-label label-info label-white"><?php echo plugin_lang_get('allowedUsersStandardLogin') ?></td>
                    <td><input type="text" class="form-control" name="prefAllowedUsersStandardLogin" value="<?php echo plugin_config_get('allowedUsersStandardLogin') ?>"></td>
                </tr>
                <!--
                <tr>
                    <td class="category"><?php //echo plugin_lang_get('redirectUri') ?></td>
                    <td><input type="text" name="redirectUri" value="<?php echo plugin_config_get('redirectUri') ?>"></td>
                </tr>
                -->
            </table>
            <div class="form-group">
                <div class="col-sm-offset-6 col-sm-8">
                    <input type="submit" value="<?php echo plugin_lang_get('save') ?>"
                            class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
</div>
<?php
layout_page_end();
?>
