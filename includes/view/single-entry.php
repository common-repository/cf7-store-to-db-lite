<?php
defined('ABSPATH') or die("No direct script allowed!!!");


$post_id = intval($post->ID);
$cf7stdb_cf7_entries = get_post_meta($post_id, 'cf7stdb_cf7_entries', true);
$cf7stdb_cf7_details = get_post_meta($post_id, 'cf7stdb_cf7_details', true);
$cf7stdb_mail_details = get_post_meta($post_id, 'cf7stdb_mail_details', true);
$cf7stdb_cf7_attach_details = get_post_meta($post_id, 'cf7stdb_cf7_attach_details', true);
$cf7stdb_cf7_id = get_post_meta($post_id, 'cf7stdb_cf7_id', true);
$read_stat = get_post_meta($post_id, 'cf7stdb_cf7_stat', true);

/*
 * All Contact form mail Details Extracted 
 */
if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
    echo __('<div id="message" class="notice notice-error is-dismissible">' . '<p>' . __('<strong>Contact form 7</strong> Plugin seems to be not installed or activated. Please activate the plugin to view details. Or, to install, you can search plugin from ', CF7STDBL_TXT_DOMAIN) . '<a href="' . esc_url(admin_url('plugin-install.php')) . '"> ' . __('here', CF7STDBL_TXT_DOMAIN) . '</a></p></div>');
    die();
}
$cf7stdb_mail_detail = $cf7stdb_mail_details->prop('mail');

if (isset($cf7stdb_mail_detail) && !empty($cf7stdb_mail_detail)) {
    $to_reciever = esc_attr($cf7stdb_mail_detail['recipient']);
    $from_sender = esc_attr($cf7stdb_mail_detail['sender']);
    $subject = esc_attr($cf7stdb_mail_detail['subject']);
    $message_body = $cf7stdb_mail_detail['body'];
    $reffered_cf_id = esc_attr($cf7stdb_mail_details->id());
    $subject_title = esc_attr($cf7stdb_mail_details->title());
}
$orginalmailstr = [
    "[your-subject]","[_site_title]"
];
$replacemailstr = [
    !empty($cf7stdb_cf7_entries['your-subject']) ? esc_attr($cf7stdb_cf7_entries['your-subject']) : '[your-subject]',
	get_bloginfo('name')
];
$email_subject_header = str_replace($orginalmailstr, $replacemailstr, $subject);

$args = array(
    'post_type' => 'wpcf7',
);

if (!empty($reffered_cf_id)) {
    $my_post = get_post($reffered_cf_id);
    $referred_cf7_form_url = '<a href="' . admin_url() . 'admin.php?page=wpcf7&post=' . intval($reffered_cf_id) . '&action=edit" target="_blank">' . esc_attr($my_post->post_title) . '</a>';
} else {
    $referred_cf7_form_url = __('Additional Entry', CF7STDBL_TXT_DOMAIN);
}
?>
<div class="cd7stdb-entries-outer-wrap" id="cd7stdb-entries-wrapper">
    <div class="cd7stdb-entries-inner-wrap" id="cd7stdb-entries-first-inn-wrap">
        <div class="cd7stdb-entries-content-inner" id="cd7stdb-entries-content-1-first-inn">
            <h3 class="cd7stdb-secondary-header"> 
                <?php _e('Contact Form Email', CF7STDBL_TXT_DOMAIN); ?>:
            </h3>
            <div class="cd7stdb-input-field-wrap">
                <label>
                    <?php _e('From', CF7STDBL_TXT_DOMAIN); ?>:
                </label>
                <div class="cd7stdb-input-field">
                    <?php echo!empty($cf7stdb_cf7_entries['your-name']) ? esc_attr($cf7stdb_cf7_entries['your-name']) : '[your-name]'; ?> [ <?php echo!empty($cf7stdb_cf7_entries['your-email']) ? esc_attr($cf7stdb_cf7_entries['your-email']) : '[your-email]'; ?> ]
                </div>
            </div>
            <div class="cd7stdb-input-field-wrap">
                <label>
                    <?php _e('To', CF7STDBL_TXT_DOMAIN); ?>:
                </label>
                <div class="cd7stdb-input-field">
                    <?php echo ($to_reciever == '[_site_admin_email]')?get_bloginfo('admin_email'):esc_attr($to_reciever); ?>
                </div>
            </div>
            <div class="cd7stdb-input-field-wrap">
                <label>
                    <?php _e('Date', CF7STDBL_TXT_DOMAIN); ?>:
                </label>
                <div class="cd7stdb-input-field">
                    <?php echo get_the_date(); ?> - <?php echo get_the_time(); ?>
                </div>
            </div>	
            <div class="cd7stdb-input-field-wrap">
                <label>
                    <?php _e('Subject', CF7STDBL_TXT_DOMAIN); ?>:
                </label>
                <div class="cd7stdb-input-field">
                    <?php echo esc_attr($email_subject_header); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="cd7stdb-entries-inner-wrap" id="cd7stdb-entries-first-inn-wrap">
        <h3 class="cd7stdb-secondary-header"> 
            <?php _e('Mail Format', CF7STDBL_TXT_DOMAIN); ?>:
        </h3>
        <div class="cd7stdb-entries-content-inner" id="cd7stdb-entries-content-1-second-inn" style="background:lightgray;">
            <div class="cd7stdb-input-field-wrap">
                <label>
                    <?php _e('Mail', CF7STDBL_TXT_DOMAIN); ?>:
                </label>
                <div class="cd7stdb-input-field">
                    <?php echo esc_attr($email_subject_header); ?>
                </div>
            </div> 

            <div class="cd7stdb-input-field-wrap">
                <label>
                    <?php _e('Message Body', CF7STDBL_TXT_DOMAIN); ?>
                </label>
                <div class="cd7stdb-input-field">
                    <?php
                    $orginal_message_body_mailstr = [
                        "[your-subject]",
                        "[your-name]",
                        "[your-email]",
                        "[your-message]",
						"[_site_title]",
						"[_site_url]"
                    ];
                    $replace_message_body_mailstr = [
                        !empty($cf7stdb_cf7_entries['your-subject']) ? esc_attr($cf7stdb_cf7_entries['your-subject']) : '[your-subject]',
                        !empty($cf7stdb_cf7_entries['your-name']) ? esc_attr($cf7stdb_cf7_entries['your-name']) : '[your-name]',
                        !empty($cf7stdb_cf7_entries['your-email']) ? esc_attr($cf7stdb_cf7_entries['your-email']) : '[your-email]',
                        !empty($cf7stdb_cf7_entries['your-message']) ? esc_attr($cf7stdb_cf7_entries['your-message']) : '[your-message]',
						get_bloginfo("name"),
						get_bloginfo("url")
                    ];
                    $email_message_body_fincont = str_replace($orginal_message_body_mailstr, $replace_message_body_mailstr, $message_body);
                    $email_message_body_filcont = $this->cf7stdbl_filter_the_message($email_message_body_fincont);
                    echo wp_kses_post($email_message_body_filcont);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="cd7stdb-entries-inner-wrap" id="cd7stdb-entries-first-inn-wrap">
        <h3 class="cd7stdb-secondary-header"> 
            <?php _e('Attachments Entries', CF7STDBL_TXT_DOMAIN); ?>:
        </h3>
        <div class="cd7stdb-entries-content-inner" id="cd7stdb-entries-content-1-second-inn">
            <div class="cd7stdb-input-field-wrap">
                <label>
                    <?php _e('Attachements', CF7STDBL_TXT_DOMAIN); ?>
                </label>
                <div class="cd7stdb-input-field">
                    <?php
                    if (isset($cf7stdb_cf7_attach_details) && !empty($cf7stdb_cf7_attach_details)) {
                        /* @var $attach_numItems type */
                        $attach_numItems = count($cf7stdb_cf7_attach_details);
                        $attach_i = 0;
                        foreach ($cf7stdb_cf7_attach_details as $key => $val) {
                            if (++$attach_i < $attach_numItems) {
                                echo '<a class = "cd7stdb-attachment-link" href = "' . esc_url(wp_get_attachment_url($val)) . '" target = "_blank">' . esc_url(wp_get_attachment_url($val)) . '</a>' . '<br/>';
                            } else {
                                echo '<a class = "cd7stdb-attachment-link" href = "' . esc_url(wp_get_attachment_url($val)) . '" target = "_blank">' . esc_url(wp_get_attachment_url($val)) . '</a>';
                            }
                        }
                    } else {
                        ?>
                        <span class="cd7stdb-attachment-link-none">
                            <?php echo __('None', CF7STDBL_TXT_DOMAIN); ?>
                        </span>
                        <?php
                    }
                    ?>
                </div>
            </div> 
        </div>
    </div>
    <div class="cd7stdb-entries-inner-wrap" id="cd7stdb-entries-second-inn-wrap">
        <h3 class="cd7stdb-secondary-header">
            <?php _e('Full Field Entries', CF7STDBL_TXT_DOMAIN); ?>
        </h3>
        <div class="cd7stdb-entries-content-inner" id="cd7stdb-entries-content-2-first-inn">
            <?php
            if (!empty($cf7stdb_cf7_entries)) {
                foreach ($cf7stdb_cf7_entries as $key => $val) {
                    if (!in_array($key, ['_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_container_post'])) {
                        ?>
                        <div class="cd7stdb-input-field-wrap">            
                            <label>
                                <strong><?php echo esc_html($key); ?>:</strong>
                            </label>
                            <div class = "cd7stdb-input-field">
                                <?php
                                if (is_array($val)) {
                                    $numItems = count($val);
                                    $i = 0;
                                    foreach ($val as $val_k => $val_v) {
                                        if (++$i < $numItems) {
                                            echo esc_attr($val_v) . ', ';
                                        } else {
                                            echo esc_attr($val_v);
                                        }
                                    }
                                } else {
                                    echo esc_attr($val);
                                }
                                ?>
                            </div>
                        </div>  
                        <?php
                    }
                }
            }
            ?>
        </div>
    </div>
    <div class="cd7stdb-entries-inner-wrap" id="cd7stdb-entries-third-inn-wrap">
        <h3 class="cd7stdb-secondary-header">
            <?php _e('Additional Entry', CF7STDBL_TXT_DOMAIN); ?>
        </h3>
        <div class="cd7stdb-entries-content-inner" id="cd7stdb-entries-content-3-first-inn">
            <?php
            if (!empty($cf7stdb_cf7_details)) {
                foreach ($cf7stdb_cf7_details as $key => $val) {
                    switch ($key) {
                        case 'wpcf7stdb_ip_address':
                            ?>
                            <div class="cd7stdb-input-field-wrap">            
                                <label>
                                    <?php _e('IP Address', CF7STDBL_TXT_DOMAIN); ?>:
                                </label>
                                <div class = "cd7stdb-input-field">
                                    <?php echo esc_attr($val); ?>
                                </div>
                            </div>
                            <?php
                            break;
                        case 'wpcf7stdb_browser_head':
                            ?>
                            <div class="cd7stdb-input-field-wrap">            
                                <label>
                                    <?php _e('User Agent Header', CF7STDBL_TXT_DOMAIN); ?>:
                                </label>
                                <div class = "cd7stdb-input-field">
                                    <?php echo esc_attr($val); ?>
                                </div>
                            </div>
                            <?php
                            break;
                        case 'wpcf7stdb_additional_browser_head':
                            ?>
                            <div class="cd7stdb-input-field-wrap">            
                                <label>
                                    <?php _e('User Agent', CF7STDBL_TXT_DOMAIN); ?>:
                                </label>
                                <div class = "cd7stdb-input-field">
                                    <?php echo esc_attr($val); ?>
                                </div>
                            </div>
                            <?php
                            break;
                        case 'refererred_page_post_link':
                            ?>
                            <div class="cd7stdb-input-field-wrap">            
                                <label>
                                    <?php _e('Referred Post', CF7STDBL_TXT_DOMAIN); ?>:
                                </label>
                                <div class = "cd7stdb-input-field">
                                    <a href="<?php echo!empty($cf7stdb_cf7_entries['_wpcf7_container_post']) ? esc_url(get_post_permalink($cf7stdb_cf7_entries['_wpcf7_container_post'])) : ':self'; ?>"
                                       target="_blank" >
                                           <?php echo!empty($cf7stdb_cf7_entries['_wpcf7_container_post']) ? esc_attr(get_the_title($cf7stdb_cf7_entries['_wpcf7_container_post'])) : 'None'; ?>
                                    </a>
                                </div>
                            </div>
                            <?php
                            break;
                        default:
                            break;
                    }
                }
            }
            ?>
            <div class="cd7stdb-input-field-wrap">            
                <label>
                    <?php _e('Referred CF7 Form', CF7STDBL_TXT_DOMAIN); ?>:
                </label>
                <div class = "cd7stdb-input-field">
                    <?php echo wp_kses_post($referred_cf7_form_url); ?>
                </div>
            </div>
        </div>
    </div>
</div>