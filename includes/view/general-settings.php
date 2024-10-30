<?php
defined('ABSPATH') or die('No script kiddies please!');
?>

<?php
$cf7_posts = $this->cf7stdbl_contact_form_7_plugin_posts();
$cd7stdb_options = get_option('cf7stdb_settings');
?>

<div class="cd7stdb-wrapper cd7stdb-clear">
    <div class="cd7stdb-head">     
        <?php include(CF7STDBL_PATH . 'includes/view/header.php'); ?> 
    </div>
    <?php
    if (isset($_GET['message']) && $_GET['message'] == '3') {
        ?>
        <div class="cf7stdb-admin-notice notice notice-success is-dismissible">
            <p><?php _e('Setting Successully Updated.', CF7STDBL_TXT_DOMAIN); ?></p>
        </div>
    <?php } else if (isset($_GET['message']) && $_GET['message'] == '4') {
        ?>
        <div class="cf7stdb-admin-notice notice notice-error is-dismissible">
            <p><?php _e('Setting Wasn\'t Updated. Please Try Again.', CF7STDBL_TXT_DOMAIN); ?></p>
        </div>           
    <?php }
    ?>
    <div class = "cd7stdb-inner-wrapper" id = "poststuff">
        <div id = "post-body-full" class = "metabox-holder columns-2">
            <div id = "post-body-content" class="cf7stdb-primary-left-container">
                <div class = "postbox">
                    <div class = "cd7stdb-menu-option-wrapper clearfix" id = "col-container">
                        <div class = "inside" id = "cd7stdb-menu-setting-wrapper">
                            <div class = "cd7stdb-header-title cd7stdb-menu-option-header-title">
                                <h3><?php _e('General Settings', CF7STDBL_TXT_DOMAIN); ?></h3>
                            </div>
                            <form action="<?php echo admin_url() . 'admin-post.php' ?>" method='post' id="cd7stdb-menu-option-form">     
                                <input type="hidden" name="action" value="cd7stdbl_save_cd7stdb_options" />
                                <div class="cd7stdb-general-setting-field-wrap">
                                    <div class="cd7stdb-input-field-wrap">
                                        <label>
                                            <h4><?php _e('Enable Storage', CF7STDBL_TXT_DOMAIN); ?>:</h4>
                                        </label>
                                        <div class="cd7stdb-input-field">
                                            <label>
                                                <input type="checkbox" class="cd7stdb-checkbox" name="cd7stdb_general_setting[cd7stdb_enable_disable]" <?php if (isset($cd7stdb_options['cd7stdb_enable_disable']) && $cd7stdb_options['cd7stdb_enable_disable'] == 'on') { ?>checked="checked"<?php } ?>/>
                                                <span class="cd7stdb-check-text"><?php _e('Enable/Disable', CF7STDBL_TXT_DOMAIN); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="cd7stdb-input-field-wrap">
                                        <label>
                                            <h4><?php _e('Enable Direct Delete of Entries Post', CF7STDBL_TXT_DOMAIN); ?>:</h4>
                                        </label>
                                        <div class="cd7stdb-input-field">
                                            <label>
                                                <input type="checkbox" class="cd7stdb-checkbox" name="cd7stdb_general_setting[cd7stdb_enable_disable_skip_trash]" <?php if (isset($cd7stdb_options['cd7stdb_enable_disable_skip_trash']) && $cd7stdb_options['cd7stdb_enable_disable_skip_trash'] == 'on') { ?>checked="checked"<?php } ?>/>
                                                <span class="cd7stdb-check-text"><?php _e('Enable/Disable', CF7STDBL_TXT_DOMAIN); ?></span>
                                            </label>
                                        </div>
                                        <p class="cd7stdb-description"><?php _e('If checked, trashing the entries will skip trash and permanently delete the entries', CF7STDBL_TXT_DOMAIN); ?></p>
                                    </div>
                                    <div class="cd7stdb-input-field-wrap">
                                        <label>
                                            <h4><?php _e('Delete Attachment While Deleting Entries', CF7STDBL_TXT_DOMAIN); ?>:</h4>
                                        </label>
                                        <div class="cd7stdb-input-field">
                                            <label>
                                                <input type="checkbox" class="cd7stdb-checkbox" name="cd7stdb_general_setting[cd7stdb_enable_disable_attach_deletion]" <?php if (isset($cd7stdb_options['cd7stdb_enable_disable_attach_deletion']) && $cd7stdb_options['cd7stdb_enable_disable_attach_deletion'] == 'on') { ?>checked="checked"<?php } ?>/>
                                                <span class="cd7stdb-check-text"><?php _e('Disable/Enable', CF7STDBL_TXT_DOMAIN); ?></span>
                                            </label>
                                        </div>
                                        <p class="cd7stdb-description"><?php _e('When checked, attachment related with the specific post will also be deleted. Only works while empting trash, or trash is skipped.', CF7STDBL_TXT_DOMAIN); ?></p>
                                    </div>
                                    <div class="cd7stdb-input-field-wrap">
                                        <label>
                                            <h4><?php _e('Disable Storing of the IP Address and Browser Data', CF7STDBL_TXT_DOMAIN); ?>:</h4>
                                        </label>
                                        <div class="cd7stdb-input-field">
                                            <label>
                                                <input type="checkbox" class="cd7stdb-checkbox" name="cd7stdb_general_setting[cd7stdb_enable_disable_disable_device_data]" <?php if (isset($cd7stdb_options['cd7stdb_enable_disable_disable_device_data']) && $cd7stdb_options['cd7stdb_enable_disable_disable_device_data'] == 'on') { ?>checked="checked"<?php } ?>/>
                                                <span class="cd7stdb-check-text"><?php _e('Disable/Enable', CF7STDBL_TXT_DOMAIN); ?></span>
                                            </label>
                                        </div>
                                        <p class="cd7stdb-description"><?php _e('If checked, user data such as public IP address and browser detail won\'t be stored, for GDPR reason', CF7STDBL_TXT_DOMAIN); ?></p>
                                    </div>
                                </div>
                                <?php wp_nonce_field('cd7stdbl_nonce_save_post_specific_storage_settings', 'cd7stdbl_add_nonce_save_post_specific_storage_settings'); ?>
                                <div class="cd7stdb-submit-wrap">
                                    <input type="submit" class="button-primary" name='cd7stdbl_save_cd7stdb_settings' value="<?php _e('Save Settings', CF7STDBL_TXT_DOMAIN); ?>" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
              <div id="postbox-container-1" class="postbox-container cf7stdb-container-sidebar">
                <?php include(CF7STDBL_PATH . 'includes/view/side-bar.php'); ?>
            </div> 
        </div>
    </div>
</div>