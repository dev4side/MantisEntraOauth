<?php
auth_reauthenticate();
access_ensure_global_level(config_get('manage_plugin_threshold'));

layout_page_header(plugin_lang_get('title'));
layout_page_begin();

print_manage_menu();

?>
<div class="form-container">
    <form action="<?php echo plugin_page('config_update') ?>" method="post">
        <?php echo form_security_field('plugin_azureoauth_config_update'); ?>
        <table>
            <tr>
                <td class="category"><?php echo plugin_lang_get('tenantId') ?></td>
                <td><input type="text" name="tenantId" value="<?php echo plugin_config_get('tenantId') ?>"></td>
            </tr>
            <tr>
                <td class="category"><?php echo plugin_lang_get('clientId') ?></td>
                <td><input type="text" name="clientId" value="<?php echo plugin_config_get('clientId') ?>"></td>
            </tr>
            <tr>
                <td class="category"><?php echo plugin_lang_get('clientSecret') ?></td>
                <td><input type="text" name="clientSecret" value="<?php echo plugin_config_get('clientSecret') ?>"></td>
            </tr>
            <!--
            <tr>
                <td class="category"><?php echo plugin_lang_get('redirectUri') ?></td>
                <td><input type="text" name="redirectUri" value="<?php echo plugin_config_get('redirectUri') ?>"></td>
            </tr>
            -->
            <tr>
                <td class="center" colspan="2"><input type="submit" class="button" value="<?php echo plugin_lang_get('update') ?>"></td>
            </tr>
        </table>
    </form>
</div>
<?php
layout_page_end();
?>
