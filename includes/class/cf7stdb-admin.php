<?php
defined('ABSPATH') or die('No script kiddies please!!');

if (!class_exists('CF7STDBL_Admin')) {

    class CF7STDBL_Admin extends CF7STDBL_Library {

        /**
         * Includes all the backend functionality
         *
         * @since 1.0.0
         */
        function __construct() {
            add_action('init', array($this, 'cf7stdbl_load_plugin_text_domain'));
            add_action('admin_notices', array($this, 'cd7stdbl_contact_form_7_plugin_required'));
            add_action('admin_enqueue_scripts', array($this, 'cd7stdbl_enqueue_scripts'));
            add_action('admin_menu', array($this, 'cd7stdbl_add_admin_menu'));
            add_action('add_meta_boxes', array($this, 'cd7stdbl_add_cf7_detail_metabox'));
            add_action('wpcf7_before_send_mail', array($this, 'cf7stdbl_save_form'));
            add_filter('manage_edit-cf7storetodbs_columns', array($this, 'cf7storetodbs_my_cpt_status_columns'));
            add_action('manage_cf7storetodbs_posts_custom_column', array($this, 'cf7storetodbs_my_cpt_status_column'), 10, 2);
            add_filter('manage_edit-cf7storetodbs_columns', array($this, 'cf7storetodbs_my_cpt_referred_cf7_columns'));
            add_action('manage_cf7storetodbs_posts_custom_column', array($this, 'cf7storetodbs_my_cpt_referred_cf7_column'), 10, 2);
            add_filter('manage_edit-cf7storetodbs_columns', array($this, 'cf7storetodbs_my_cpt_referred_columns'));
            add_action('manage_cf7storetodbs_posts_custom_column', array($this, 'cf7stdbl_my_cpt_referred_column'), 10, 2);
            add_filter('post_row_actions', array($this, 'cf7stdbl_modify_list_row_actions'), 10, 2);
            add_action('admin_menu', array($this, 'cf7stdbl_remove_admin_submenus'), 999);
            add_action('admin_menu', array($this, 'cf7stdbl_remove_my_post_metaboxes'));
            add_action('admin_head-post.php', array($this, 'cf7stdbl_posttype_admin_css'));
            add_action('quick_edit_custom_box', array($this, 'cf7stdbl_display_custom_quickedit_entries'), 10, 2);
            add_action('init', array($this, 'cf7stdbl_func_export_all_entries'));
            add_filter('views_edit-cf7storetodbs', array($this, 'cf7stdbl_custom_list_link_wpse_cf7storetodbs'));
            add_filter('query_vars', array($this, 'cf7stdbs_register_query_vars'));
            add_action('restrict_manage_posts', array($this, 'cf7stdbs_restrict_posts_by_metavalue'));
            add_action('pre_get_posts', array($this, 'cf7stdbs_pre_get_posts'));
            add_action('admin_post_cd7stdbl_save_cd7stdb_options', array($this, 'cd7stdbl_save_cd7stdb_options'));
            add_action('before_delete_post', array($this, 'cd7stdbl_delete_attachment_while_deleting_post'));
            add_action('admin_post_cd7stdbl_generate_cd7stdb_report_options', array($this, 'cd7stdbl_generate_cd7stdb_report_options'));
            add_action('wp_ajax_cd7stdbl_entry_status', array($this, 'cd7stdbl_entry_status'));
            add_action('restrict_manage_posts', array($this, 'add_export_button'));
            add_filter( 'plugin_row_meta', array( $this, 'cf7stdb_plugin_row_meta' ), 10, 2 );
            add_filter( 'admin_footer_text', array( $this, 'cf7stdb_admin_footer_text' ) );
        }

        function cf7stdb_plugin_row_meta( $links, $file ){

            if ( strpos( $file, 'contact-form-7-store-to-db-lite.php' ) !== false ) {
                $new_links = array(
                    'demo' => '<a href="http://demo.accesspressthemes.com/wordpress-plugins/contact-form-7-store-to-db-lite" target="_blank"><span class="dashicons dashicons-welcome-view-site"></span>Live Demo</a>',
                    'doc' => '<a href="https://accesspressthemes.com/documentation/contact-form-7-store-to-db-lite" target="_blank"><span class="dashicons dashicons-media-document"></span>Documentation</a>',
                    'support' => '<a href="http://accesspressthemes.com/support" target="_blank"><span class="dashicons dashicons-admin-users"></span>Support</a>',
                    'pro' => '<a href="https://accesspressthemes.com/wordpress-plugins/contact-form-7-store-to-db/" target="_blank"><span class="dashicons dashicons-cart"></span>Premium version</a>'
                );

                $links = array_merge( $links, $new_links );
            }

            return $links;
        }
        function cf7stdb_admin_footer_text( $text ){
            global $post;
            if ((isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'cf7storetodbs' ) ) {
                $link = 'https://wordpress.org/support/plugin/cf7-store-to-db-lite/reviews/#new-post';
                $pro_link = 'https://accesspressthemes.com/wordpress-plugins/contact-form-7-store-to-db/';
                $text = 'Enjoyed <strong>Form Store TO DB</strong> ? <a href="' . $link . '" target="_blank">Please leave us a ★★★★★ rating</a> We really appreciate your support! | Try premium version - <a href="' . $pro_link . '" target="_blank">Contact Form 7 Store to DB</a> - more features, more power!';
                return $text;
            } else {
                return $text;
            }
        }

        function cf7stdbl_load_plugin_text_domain() {
            load_plugin_textdomain(CF7STDBL_TXT_DOMAIN, false, basename(dirname(__FILE__)) . '/languages');
        }

        /*
         * Function to export all the entries 
         */
        function cf7stdbl_func_export_all_entries() {
            global $post;
            $cd7stdb_options = get_option('cf7stdb_settings');
            if (isset($_GET['export_cf7_entries']) && current_user_can('manage_options')) {
                $cf7id = intval(sanitize_text_field($_GET['cf7_id']));
                $read_stat = sanitize_text_field($_GET['cf7_stat']);

                if (empty($meta_query)) {
                    $meta_query = array();
                }
                if ('' == $cf7id && '' == $read_stat) {
                    $args = array(
                        'post_type' => array('cf7storetodbs'),
                        'order' => 'ASC',
                        'orderby' => 'title'
                    );
                } else if ('' != $cf7id && '' != $read_stat) {
                    $args = array(
                        'post_type' => array('cf7storetodbs'),
                        'order' => 'ASC',
                        'orderby' => 'title',
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => 'cf7stdb_cf7_id',
                                'value' => $cf7id,
                                'compare' => '='
                            ),
                            array(
                                'key' => 'cf7stdb_cf7_stat',
                                'value' => $read_stat,
                                'compare' => '='
                            )
                        )
                    );
                } else if ('' == $cf7id && '' != $read_stat) {
                    $args = array(
                        'post_type' => array('cf7storetodbs'),
                        'order' => 'ASC',
                        'orderby' => 'title',
                        'meta_query' => array(
                            array(
                                'key' => 'cf7stdb_cf7_stat',
                                'value' => $read_stat,
                                'compare' => '='
                            )
                        )
                    );
                } else if ('' != $cf7id && '' == $read_stat) {
                    $args = array(
                        'post_type' => array('cf7storetodbs'),
                        'order' => 'ASC',
                        'orderby' => 'title',
                        'meta_query' => array(
                            array(
                                'key' => 'cf7stdb_cf7_id',
                                'value' => $cf7id,
                                'compare' => '='
                            )
                        )
                    );
                } else if ('' == $cf7id && '' == $read_stat) {
                    $args = array(
                        'post_type' => array('cf7storetodbs'),
                        'order' => 'ASC',
                        'orderby' => 'title',
                    );
                }
                $query = new WP_Query($args);
                $arr_posts = get_posts($args);
                if ($arr_posts) {
                    $data_rows = array();
                    $row = array();
                    foreach ($arr_posts as $arr_post => $arr_val) {
                        setup_postdata($post);
                        $post_id = $arr_val->ID;
                        $cf7stdb_cf7_entries = get_post_meta($post_id, 'cf7stdb_cf7_entries', true);
                        $cf7stdb_cf7_details = get_post_meta($post_id, 'cf7stdb_cf7_details', true);
                        $cf7stdb_mail_details = get_post_meta($post_id, 'cf7stdb_mail_details', true);
                        $cf7stdb_cf7_attach_details = get_post_meta($post_id, 'cf7stdb_cf7_attach_details', true);
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
                        $args = array(
                            'post_type' => 'wpcf7',
                        );
                        $custom_date_and_time = get_the_date('F j, Y g:i a', $post_id);
                        if (!is_super_admin()) {
                            return;
                        }

                        $header_row = array(
                            0 => __('From', CF7STDBL_TXT_DOMAIN),
                            1 => __('From Email', CF7STDBL_TXT_DOMAIN),
                            2 => __('To', CF7STDBL_TXT_DOMAIN),
                            3 => __('Date', CF7STDBL_TXT_DOMAIN),
                            4 => __('Subject', CF7STDBL_TXT_DOMAIN),
                            5 => __('Message Body', CF7STDBL_TXT_DOMAIN),
                            6 => __('IP Address', CF7STDBL_TXT_DOMAIN),
                            7 => __('User Agent Header', CF7STDBL_TXT_DOMAIN),
                            8 => __('User Agent', CF7STDBL_TXT_DOMAIN),
                            9 => __('Referred Post', CF7STDBL_TXT_DOMAIN),
                            10 => __('Attachment', CF7STDBL_TXT_DOMAIN),
                        );
                        if (!empty($cf7stdb_cf7_entries)) {
                            $row_count = 11;
                            foreach ($cf7stdb_cf7_entries as $key => $val) {
                                if (!in_array($key, ['_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_container_post'])) {
                                    if (is_array($val)) {
                                        array_push($header_row, $key);
                                    } else {
                                        array_push($header_row, $key);
                                    }
                                }
                                $row_count++;
                            }
                        }
                        $row[0] = !empty($cf7stdb_cf7_entries['your-name']) ? esc_attr($cf7stdb_cf7_entries['your-name']) : '[your-name]';
                        $row[1] = !empty($cf7stdb_cf7_entries['your-email']) ? esc_attr($cf7stdb_cf7_entries['your-email']) : '[your-email]';
                        $row[2] = $to_reciever;
                        $row[3] = $custom_date_and_time;
                        $row[4] = $email_subject_header;
                        $row[5] = $email_message_body_filcont;
                        /* @var $cf7stdb_cf7_details type */
                        $row[6] = $cf7stdb_cf7_details['wpcf7stdb_ip_address'];
                        $row[7] = $cf7stdb_cf7_details['wpcf7stdb_browser_head'];
                        $row[8] = $cf7stdb_cf7_details['wpcf7stdb_additional_browser_head'];
                        $row[9] = $cf7stdb_cf7_details['refererred_page_post_link'];
                        $cf7stdb_cf7_attach = '';
                        $attachment_i = 0;
                        if (isset($cf7stdb_cf7_attach_details) && !empty($cf7stdb_cf7_attach_details)) {
                            /* @var $attach_numItems type */
                            $attach_numItems = count($cf7stdb_cf7_attach_details);
                            foreach ($cf7stdb_cf7_attach_details as $key => $val) {
                                if (++$attachment_i < $attach_numItems) {
                                    $cf7stdb_cf7_attach .= wp_get_attachment_url($val) . ', ';
                                } else {
                                    $cf7stdb_cf7_attach .= wp_get_attachment_url($val);
                                }
                            }
                        } else {
                            $cf7stdb_cf7_attach = __('None', CF7STDBL_TXT_DOMAIN);
                        }
                        $row[10] = $cf7stdb_cf7_attach;

                        if (!empty($cf7stdb_cf7_entries)) {
                            $row_count_val = 11;
                            foreach ($cf7stdb_cf7_entries as $key => $val) {
                                if (!in_array($key, ['_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_container_post'])) {
                                    if (is_array($val)) {
                                        $row[$row_count_val] = implode(",", $val);
                                    } else {
                                        $row[$row_count_val] = esc_attr($val);
                                    }
                                }
                                $row_count_val++;
                            }
                        }
                        $data_rows[] = $row;
                        /* Group Export Ends */
                    }
                }
                $file_header_orginalstr = array("#current_timestamp");
                $file_header_replacestr = array(time());
                $filename = isset($cd7stdb_options['cd7stdb_group_csv_title']) && !empty($cd7stdb_options['cd7stdb_group_csv_title']) ? str_replace($file_header_orginalstr, $file_header_replacestr, esc_attr($cd7stdb_options['cd7stdb_group_csv_title'])) . '.csv' : 'cf7stdb-group-entries-' . time() . '.csv';
                $fh = @fopen('php://output', 'w');
                header_remove();
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Description: File Transfer');
                header('Content-type: text/csv; charset=utf-8');
                header("Content-Disposition: attachment; filename={$filename}");
                header('Expires: 0');
                header('Pragma: public');
                fputcsv($fh, $header_row);
                foreach ($data_rows as $data_row) {
                    fputcsv($fh, $data_row);
				}
                fclose($fh);
                die();
            }
        }

        function add_export_button() {
            global $current_screen, $pagenow;
            $screen = get_current_screen();
            if ('cf7storetodbs' != $current_screen->post_type) {
                return;
            }
            if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
                ?>
                <script type="text/javascript">
                    jQuery(function ($) {
                        var html = '<input type="submit" name="export_cf7_entries" id="export_all_posts" class="button button-primary" value="<?php echo __("Export Entries As CSV", CF7STDBL_TXT_DOMAIN); ?> "/>';
                        if (!$('body').find('table.wp-list-table').find('tr').hasClass('no-items')) {
                            $(html).insertAfter('#post-query-submit');
                        }
                    });
                </script>
                <?php
            } else {
                ?>
                <script type="text/javascript">
                    jQuery(function ($) {
                        var html = '<input type="submit" id="export_all_posts" class="button button-primary" value="<?php echo __("Export Entries As CSV", CF7STDBL_TXT_DOMAIN); ?> "/>';
                        if (!$('body').find('table.wp-list-table').find('tr').hasClass('no-items')) {
                            $(html).insertAfter('#post-query-submit');
                            $("#export_all_posts").attr("disabled", "disabled");
                        }
                    });
                </script>
                <?php
            }
        }

        /*
         * Admin Enqueue script 
         */

        function cd7stdbl_enqueue_scripts() {
            global $current_screen, $pagenow;
            if ('cf7storetodbs' != $current_screen->post_type) {
                return;
            }
            $cd7stdbl_admin_ajax_nonce = wp_create_nonce('cd7stdbl-admin-ajax-nonce');
            $cd7stdbl_admin_ajax_object = array('ajax_url' => admin_url('admin-ajax.php'), 'ajax_nonce' => $cd7stdbl_admin_ajax_nonce);
            wp_enqueue_script('cd7stdbl-admin-script', CF7STDBL_JS_DIR . 'admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), CF7STDBL_TXT_DOMAIN, false);
            wp_localize_script('cd7stdbl-admin-script', 'cd7stdbl_admin_js_params', $cd7stdbl_admin_ajax_object);
            wp_enqueue_script('cd7stdbl-chart-min', CF7STDBL_JS_DIR . 'Chart.min.js', array('jquery'), CF7STDBL_TXT_DOMAIN);
            wp_enqueue_script('cd7stdbl-htmltocanvas-min', CF7STDBL_JS_DIR . 'html2canvas.min.js', array('jquery'), CF7STDBL_TXT_DOMAIN);
            wp_enqueue_script('jquery-ui-datepicker');
            /* Enqueue Plugin Style */
            wp_enqueue_style('cd7stdbl-font-awesome-free', CF7STDBL_CSS_DIR . 'all.css', array(), CF7STDBL_TXT_DOMAIN);
            wp_enqueue_style('cd7stdbl-font-awesome-min', CF7STDBL_CSS_DIR . 'fontawesome.min.css', array(), CF7STDBL_TXT_DOMAIN);
            wp_enqueue_style('cd7stdbl-admin-css', CF7STDBL_CSS_DIR . 'plugin-style.css', array(), CF7STDBL_TXT_DOMAIN);
            wp_enqueue_style('cd7stdbl-jquery-ui-css', CF7STDBL_CSS_DIR . 'jquery-ui.css');
        }

        /**
         * Function to save Post specific storage
         */
        function cd7stdbl_delete_attachment_while_deleting_post($postid) {

// We check if the global post type isn't ours and just return
            global $post_type;
            if ($post_type != 'cf7storetodbs')
                return;
            global $wpdb;

            $args = array(
                'post_type' => 'attachment',
                'post_status' => 'any',
                'posts_per_page' => -1,
                'post_parent' => $post_id
            );
            $cf7stdb_cf7_attach_details = get_post_meta($postid, 'cf7stdb_cf7_attach_details', true);
            if (!empty($cf7stdb_cf7_attach_details) && current_user_can('manage_options')) :
                $delete_attachments_query = $wpdb->prepare('DELETE FROM %1$s WHERE %1$s.ID IN (%2$s)', $wpdb->posts, join(',', $cf7stdb_cf7_attach_details));
            $wpdb->query($delete_attachments_query);
        endif;
    }

        /**
         * Function to save Post specific storage
         */
        function cd7stdbl_save_cd7stdb_options() {
            global $wpdb;

            if (isset($_POST['cd7stdbl_add_nonce_save_post_specific_storage_settings']) && isset($_POST['cd7stdbl_save_cd7stdb_settings']) && wp_verify_nonce($_POST['cd7stdbl_add_nonce_save_post_specific_storage_settings'], 'cd7stdbl_nonce_save_post_specific_storage_settings') && current_user_can('manage_options')) {
                foreach ($_POST as $key => $val) {
                    if ($key == 'cd7stdb_general_setting') {
                        $$key = $this->cf7stdbl_sanitize_array($val);
                    } else {
                        $$key = sanitize_text_field($val);
                    }
                }
                $cd7stdb_settings_array = array();
                if (isset($cd7stdb_general_setting) && !empty($cd7stdb_general_setting)) {
                    foreach ($cd7stdb_general_setting as $key => $val) {
                        if (!is_array($val)) {
                            $cd7stdb_template_settings_array[$key] = sanitize_text_field($val);
                        } else {
                            $cd7stdb_settings_array[$key] = array();
                            foreach ($val as $v) {
                                if (!is_array($v)) {
                                    $cd7stdb_template_settings_array[$key][] = sanitize_text_field($v);
                                } else {
                                    $cd7stdb_template_settings_array[$key][] = array_map('sanitize_text_field', $v);
                                }
                            }
                        }
                    }
                }
                $update = update_option('cf7stdb_settings', $cd7stdb_template_settings_array);
                if ($update) {//if update success
                    wp_redirect(admin_url() . 'edit.php?post_type=cf7storetodbs&page=cf7stdbl_general&message=3');
                } else {//if update failure
                    wp_redirect(admin_url() . 'edit.php?post_type=cf7storetodbs&page=cf7stdbl_general&message=4');
                }
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Function to Generate Entries Report
         */
        function cd7stdbl_generate_cd7stdb_report_options() {
            if (isset($_POST['cd7stdbl_generate_cd7stdb_rep_add_nonce']) && isset($_POST['cd7stdbl_generate_report_button']) && wp_verify_nonce($_POST['cd7stdbl_generate_cd7stdb_rep_add_nonce'], 'cd7stdbl_generate_cd7stdb_rep_nonce')) {
                $cf7_form_id = !empty($_POST['entries_post_form_specific']) ? sanitize_text_field($_POST['entries_post_form_specific']) : '';
                $entries_year_specific = !empty($_POST['entries_year_specific']) ? sanitize_text_field($_POST['entries_year_specific']) : '';
                wp_redirect(admin_url() . 'edit.php?post_type=cf7storetodbs&page=cf7stdbl_reports&action=indiv_report&cf7_form_id=' . $cf7_form_id . '&cf7_year=' . $entries_year_specific);
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Query var for filter
         *
         * @since 1.0.0
         */
        function cf7stdbs_register_query_vars($qvars) {
//Add these query variables
            $qvars[] = 'cf7_id';
            $qvars[] = 'cf7_stat';
            return $qvars;
        }

        /**
         * Post Filter by Custom Metavalue
         *
         * @since 1.0.0
         */
        function cf7stdbs_restrict_posts_by_metavalue() {
            global $typenow;
            if ($typenow == 'cf7storetodbs') {
                $selected_id = get_query_var('cf7_id');
                $cf7stdb_cf7_posts = $this->cf7stdbl_contact_form_7_plugin_posts();
                $selected_stat = get_query_var('cf7_stat');
                $output = "<select style='width:150px' name='cf7_id' class='postform'>\n";
                $output .= '<option ' . selected($selected_id, 0, false) . ' value="">' . __('Show All Form', CF7STDBL_TXT_DOMAIN) . '</option>';
                foreach ($cf7stdb_cf7_posts as $cf7stdb_cf7p_key => $cf7stdb_cf7p_val):
                    $output .= "<option value='{$cf7stdb_cf7p_key}' " . selected($selected_id, $cf7stdb_cf7p_key, false) . '>' . $cf7stdb_cf7p_val . '</option>';
                endforeach;
                $output .= "</select>\n";

                $output .= "<select style='width:150px' name='cf7_stat' class='stat-postform'>\n";
                $output .= '<option ' . selected($selected_stat, 0, false) . ' value="">' . __('All Forms Status', CF7STDBL_TXT_DOMAIN) . '</option>';
                $output .= '<option value="unread" ' . selected($selected_stat, 'unread', false) . '>' . __('Unread', CF7STDBL_TXT_DOMAIN) . '</option>';
                $output .= '<option value="read" ' . selected($selected_stat, 'read', false) . '>' . __('Read', CF7STDBL_TXT_DOMAIN) . '</option>';
                $output .= "</select>\n";
                echo $output;
            }
        }

        function cf7stdbs_pre_get_posts($query) {
            $cf7id = $query->get('cf7_id');
            $read_stat = $query->get('cf7_stat');
            if ('' == $cf7id && '' == $read_stat) {
                return $query;
            }

            if (empty($meta_query)) {
                $meta_query = array();
            }
            if ('' != $cf7id && '' != $read_stat) {
                $meta_query[] = array(
                    'post_type' => array('cf7storetodbs'),
                    'order' => 'ASC',
                    'orderby' => 'title',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'cf7stdb_cf7_id',
                            'value' => $cf7id,
                            'compare' => '='
                        ),
                        array(
                            'key' => 'cf7stdb_cf7_stat',
                            'value' => $read_stat,
                            'compare' => '='
                        )
                    )
                );
            } else if ('' == $cf7id && '' != $read_stat) {
                $meta_query[] = array(
                    'post_type' => array('cf7storetodbs'),
                    'order' => 'ASC',
                    'orderby' => 'title',
                    'meta_query' => array(
                        array(
                            'key' => 'cf7stdb_cf7_stat',
                            'value' => $read_stat,
                            'compare' => '='
                        )
                    )
                );
            } else if ('' != $cf7id && '' == $read_stat) {
                $meta_query[] = array(
                    'post_type' => array('cf7storetodbs'),
                    'order' => 'ASC',
                    'orderby' => 'title',
                    'meta_query' => array(
                        array(
                            'key' => 'cf7stdb_cf7_id',
                            'value' => $cf7id,
                            'compare' => '='
                        )
                    )
                );
            }

            $query->set('meta_query', $meta_query);
//            var_dump($query);
//            die();
        }

        /* Customize link in WordPress */

        function cf7stdbl_custom_list_link_wpse_cf7storetodbs($views) {
            $views['dashboard'] = '<a class="button secondary-button" href="' . admin_url('edit.php?post_type=cf7storetodbs&page=cf7stdbl_reports') . '">' . '<span class="dashicons dashicons-chart-bar"></span> ' . __('Go to Entry Report', CF7STDBL_TXT_DOMAIN) . '</a>';
            return $views;
        }

        function cf7stdb_filter_attachment($wpcf7_form) {
            $mail_tags = $wpcf7_form->prop('mail');
            $mail_fields = wpcf7_mail_replace_tags($mail_tags);
// save any attachments to a temp directory
            $mail_string = trim($mail_fields['attachments']);
            if (strlen($mail_string) > 0 and ! ctype_space($mail_string)) {
                $mail_attachments = explode(" ", $mail_string);
                foreach ($mail_attachments as $attachment) {
                    $uploaded_file_path = ABSPATH . 'wp-content/uploads/wpcf7_uploads/' . $attachment;
                    $new_filepath = WPCF7EV_UPLOADS_DIR . $attachment;
                    rename($uploaded_file_path, $new_filepath);
                }
            }
        }

        function cf7stdb_group_csv($wpcf7_form) {
            $cf7stdb_export_indiv_nonce = $_REQUEST['_wpnonce'];
            $post_id = sanitize_text_field(intval($_GET['post_id']));
            $action = sanitize_text_field($_GET['action']);
            if (wp_verify_nonce($_REQUEST['_wpnonce'], 'cf7stdb_export_nonce')) {
                $this->download_group_csv(sanitize_text_field($_GET['post_id']), $action);
                die();
            } else {
                die('No script kiddies please!');
            }
        }

        function cf7stdbl_display_custom_quickedit_entries($column_name, $post) {
            $quick_preview_id = '#quick-preview-' . $post->ID;
            ?>
            <fieldset class="inline-edit-col-right inline-edit-entries" id="<?php echo esc_attr($quick_preview_id); ?>">
                <div class="inline-edit-col column-<?php echo esc_attr($column_name); ?>">
                    <label class="inline-edit-group">
                    </label>
                </div>
            </fieldset>
            <?php
        }

        /**
         * Disable and Hide Unwanted div
         * @global type $post_type
         */
        function cf7stdbl_posttype_admin_css() {
            global $post_type;
            $post_types = array(
                /* set post types */
                'cf7storetodbs',
            );
            if (in_array($post_type, $post_types))
                echo '<style type="text/css">div#side-sortables{display: none;}</style>';
        }

        /**
         * Remove all admin sub menu inside the plugin
         */
        function cf7stdbl_remove_admin_submenus() {
            remove_submenu_page('cf7storetodbs', 'certain-plugin-settings');
        }

        /**
         * Remove Default Custom Post Type action in the entries list
         */
        function cf7stdbl_modify_list_row_actions($actions, $post) {
            $cd7stdb_options = get_option('cf7stdb_settings');
            $post_stat = isset($_GET['post_status']) && !empty($_GET['post_status']) ? sanitize_text_field($_GET['post_status']) : '';
            if ($post->post_type == "cf7storetodbs" && $post_stat != 'trash') {

                $url = admin_url('post.php?post=' . $post->ID);

                $quick_preview_url = '#quick-preview-' . $post->ID;
                $edit_link = add_query_arg(array('action' => 'edit'), $url);
                $trash_status = isset($cd7stdb_options['cd7stdb_enable_disable_skip_trash']) && $cd7stdb_options['cd7stdb_enable_disable_skip_trash'] == 'on' ? true : false;

                $trash_link = get_delete_post_link($post->ID, '', $trash_status);

                $export_link = admin_url('admin-post.php?post_id=' . $post->ID);
                $export_nonce = wp_create_nonce('cf7stdb_export_nonce');
                $single_csv_link_url = add_query_arg(array('action' => 'cf7stdbl_indi_csv'), $export_link);
                if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
                    $single_csv_link = add_query_arg(array('_wpnonce' => $export_nonce), $single_csv_link_url);
                } else {
                    $single_csv_link = '#';
                }

                //$trash = $actions['trash'];

                /*
                 * Reseting the default $actions with own array
                 * new link 'Copy'
                 */
                $actions = array(
                    'edit' => sprintf('<a class="row-title cf7stdb-edit-link" href="%1$s">%2$s</a>', esc_url($edit_link), esc_html(__('View Detail', CF7STDBL_TXT_DOMAIN))),
                    'trash' => sprintf('<a class="cf7stdb-trash-link" href="%1$s">%2$s</a>', esc_url($trash_link), esc_html(__('Trash Entry', CF7STDBL_TXT_DOMAIN))),
                    /*    'export' => sprintf('<a class="cf7stdb-export-link" href="%1$s">%2$s</a>', esc_url($single_csv_link), esc_html(__('Export Entry as CSV', CF7STDBL_TXT_DOMAIN))) */
                );

                if (current_user_can('edit_my_cf7storetodbs', $post->ID)) {


                    $copy_link = wp_nonce_url(add_query_arg(array('action' => 'copy'), $url), 'edit_my_cf7storetodbs_nonce');

                    $actions = array_merge($actions, array(
                        'copy' => sprintf('<a href="%1$s">%2$s</a>', esc_url($copy_link), 'Duplicate'
                    )
                    ));

                    $actions['trash'] = $trash;
                }
            }

            return $actions;
        }

        /**
         * CF7 Store To DB menu in backend
         *
         * @since 1.0.0
         */
        function cd7stdbl_add_admin_menu() {
            $page_title = (isset($_GET['id'], $_GET['action']) && $_GET['action'] == 'view') ? __('View Entries', CF7STDBL_TXT_DOMAIN) : __('All Entries', CF7STDBL_TXT_DOMAIN);
            add_submenu_page(
                'edit.php?post_type=cf7storetodbs', __('General Setting', CF7STDBL_TXT_DOMAIN), __('General Settings', CF7STDBL_TXT_DOMAIN), 'manage_options', 'cf7stdbl_general', array($this, 'cf7stdbl_general')
            );
            add_submenu_page(
                'edit.php?post_type=cf7storetodbs', __('Entry Report', CF7STDBL_TXT_DOMAIN), __('Entry Report', CF7STDBL_TXT_DOMAIN), 'manage_options', 'cf7stdbl_reports', array($this, 'cf7stdbl_report')
            );
            add_submenu_page(
                'edit.php?post_type=cf7storetodbs', __('More WP Resources', CF7STDBL_TXT_DOMAIN), __('More WP Resources', CF7STDBL_TXT_DOMAIN), 'manage_options', 'cf7stdbl_about', array($this, 'cf7stdbl_about')
            );
            $existing_CPT_menu = 'wpcf7';
            $link_our_new_CPT = 'edit.php?post_type=cf7storetodbs';
        }

        function cd7stdbl_contact_form_7_plugin_required($param) {
            if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
                $notice_screens = array(
                    'plugins',
                    'dashboard',
                );

                $screen = get_current_screen();

                if (!in_array($screen->id, $notice_screens)) {
                    return;
                }

                echo '<div id="message" class="notice notice-error is-dismissible">' . '<p>' . __('<strong>Contact form 7</strong> Plugin Required. <strong>CF7</strong> plugin either not installed or activated. To install, you can search plugin from ', CF7STDBL_TXT_DOMAIN) . '<a href="' . esc_url(admin_url('plugin-install.php')) . '"> ' . __('here', CF7STDBL_TXT_DOMAIN) . '</a></p></div>';
            }
        }

        /**
         * General Setting page 
         */
        function cf7stdbl_general() {
            include(CF7STDBL_PATH . 'includes/view/general-settings.php');
        }

        function cf7stdbl_report() {
            include(CF7STDBL_PATH . 'includes/view/entries-report.php');
        }
        
        function cf7stdbl_about() {
            include(CF7STDBL_PATH . 'includes/view/about.php');
        }
        

        function cd7stdbl_add_cf7_detail_metabox() {
            add_meta_box('cf7stdb_detail_disp_option', __('Full Entry Details', CF7STDBL_TXT_DOMAIN), array($this, 'cf7stdbl_add_metafield_to_store_data'), 'cf7storetodbs', 'normal', 'default');
        }

        /*
         * General Setting Metabox callback
         */

        function cf7stdbl_add_metafield_to_store_data($post) {
            wp_nonce_field(basename(__FILE__), 'cf7stdb_settings_nonce');
            $stored_entries_values = get_post_meta($post->ID);
            include(CF7STDBL_PATH . 'includes/view/single-entry.php');
        }

        /*
         * Function to store all the entry detail into post
         */

        function cf7stdbl_save_form() {
            $cd7stdb_options = get_option('cf7stdb_settings');
            if (isset($cd7stdb_options['cd7stdb_enable_disable']) && $cd7stdb_options['cd7stdb_enable_disable'] == 'on') {

                $cf7data = WPCF7_Submission::get_instance();
                $mail_tags = '';
                if ($cf7data) {
                    $cf7finaldata ['posted_data'] = $cf7data ? $cf7data->get_posted_data() : null;
                    $cf7finaldata['contact_form'] = $cf7data ? $cf7data->get_contact_form() : null;
                    $cf7finaldata['attachment_data'] = $cf7data ? $cf7data->uploaded_files() : null; // this allows you access to the upload file in the temp location
                }
                $cf7stdb_cf7_id = intval($cf7finaldata ['contact_form']->id());

                $get_browser_headerpost = isset($cf7finaldata ['posted_data']['your-subject']) && !empty($cf7finaldata ['posted_data']['your-subject']) ? sanitize_text_field($cf7finaldata ['posted_data']['your-subject']) : '[your-subject]';
                $get_referred_post_id = isset($cf7finaldata ['posted_data']['_wpcf7_container_post']) && !empty($cf7finaldata ['posted_data']['_wpcf7_container_post']) ? sanitize_text_field(intval($cf7finaldata ['posted_data']['_wpcf7_container_post'])) : '';
                $cd7stdb_post_specific_entry = isset($cd7stdb_options['cd7stdb_post_specific_entry']) && !empty($cd7stdb_options['cd7stdb_post_specific_entry']) ? $cd7stdb_options['cd7stdb_post_specific_entry'] : array();
                if (!in_array($cf7stdb_cf7_id, $cd7stdb_post_specific_entry) || $cd7stdb_options['cd7stdb_post_specific_entry'] === NULL) {
                    $args = ['post_type' => 'cf7storetodbs',
                    'post_title' => $get_browser_headerpost,
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_author' => 1,
                ];

                $post_id = wp_insert_post($args);
                if (!is_wp_error($post_id)) {
                        /*
                         * @var $cf7stdb_cf7_entries type 
                         * @var $cf7stdb_mail_entries type 
                         */
                        $cf7stdb_mail_entries = (array) $mail_tags;
                        $get_ipaddress = $this->cf7stdbl_getip();
                        /*
                         * @var $get_browser_header type 
                         */
                        $get_browser_header = $this->cf7stdbl_browser_header();

                        /*
                         * @var $get_ipaddress type 
                         */
                        $cf7stdb_cf7_additional_detail = [
                            'wpcf7stdb_referred_page_id' => sanitize_text_field(intval($get_referred_post_id)),
                            'refererred_page_post_link' => sanitize_text_field($_SERVER['HTTP_REFERER']),
                        ];

                        if (!isset($cd7stdb_options['cd7stdb_enable_disable_disable_device_data'])) {
                            /*
                             * @var $_SERVER type 
                             */
                            $final_user_data_array = $cf7stdb_cf7_additional_detail + array('wpcf7stdb_ip_address' => sanitize_text_field($get_ipaddress),
                                'wpcf7stdb_browser_head' => sanitize_text_field($get_browser_header),
                                'wpcf7stdb_additional_browser_head' => sanitize_text_field($_SERVER['HTTP_USER_AGENT']),
                                'wpcf7stdb_remote_addr' => sanitize_text_field($_SERVER['REMOTE_ADDR']));
                        } else {
                            $final_user_data_array = $cf7stdb_cf7_additional_detail;
                        }

                        $wpcf7stdb_stat = 'unread';
                        /*
                         *  save data into metabox
                         */
                        update_post_meta($post_id, 'cf7stdb_cf7_entries', $cf7finaldata ['posted_data']);
                        update_post_meta($post_id, 'cf7stdb_cf7_id', $cf7stdb_cf7_id);
                        update_post_meta($post_id, 'cf7stdb_cf7_stat', $wpcf7stdb_stat);
                        update_post_meta($post_id, 'cf7stdb_mail_details', $cf7finaldata['contact_form']);
                        update_post_meta($post_id, 'cf7stdb_cf7_details', $final_user_data_array);

                        //update_post_meta($post_id, "cf7stdb_cf7_attach_details", $cf7finaldata['attachment_data']);

                        /**
                         * Attachment Upload query
                         */
                        if (isset($cd7stdb_options['cd7stdb_enable_disable_attachment']) || !isset($cd7stdb_options['cd7stdb_enable_disable_attachment'])) {
                            if (isset($cf7finaldata['attachment_data']) && !empty($cf7finaldata['attachment_data'])) {
                                $atachemnt_input_array = array();
                                foreach ($cf7finaldata['attachment_data'] as $key => $val) {
                                    $cf7_file_field_name = $cf7finaldata[$key]; // [file uploadyourfile]
                                    //Do the magic the same as the refer link above
                                    foreach ($val as $k => $v){
                                        $image_name = basename($v);
                                        $image_location = $v;
                                        $image_content = file_get_contents($image_location);
										$wud = wp_upload_dir();
                                    	$upload = wp_upload_bits($image_name, null, $image_content);
                                    	$chemin_final = $upload['url'];
                                    	$filename = $upload['file'];
										
										if ($filename > '') {
											require_once(ABSPATH . 'wp-admin/includes/admin.php');
											$wp_filetype = wp_check_filetype(basename($filename), null);
											$attachment = array(
												'post_mime_type' => $wp_filetype['type'],
												'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
												'post_content' => '',
												'post_status' => 'inherit'
											);
											$attach_id = wp_insert_attachment($attachment, $filename, $newpostid);
											require_once(ABSPATH . 'wp-admin/includes/image.php');
											$attach_data = wp_generate_attachment_metadata($attach_id, $filename);
											wp_update_attachment_metadata($attach_id, $attach_data);
											array_push($atachemnt_input_array, $attach_id);

											update_post_meta($post_id, "cf7stdb_cf7_attach_details", $atachemnt_input_array);
										} else {
											update_post_meta($post_id, "cf7stdb_cf7_attach_details", $atachemnt_input_array);
										}
                                    } // second for each
                                }
                            }
                        } else {
                            update_post_meta($post_id, "cf7stdb_cf7_attach_details", array());
                        }
                    }
                } else {
                    //there was an error in the post insertion, 
                    //echo $post_id->get_error_message();
                }
            }
        }

        /*
         *  REMOVE POST META BOXES
         */

        function cf7stdbl_remove_my_post_metaboxes() {
            global $post_type;
            remove_meta_box('authordiv', 'cf7storetodbs', 'normal'); // Author Metabox
            remove_meta_box('commentstatusdiv', 'cf7storetodbs', 'normal'); // Comments Status Metabox
            remove_meta_box('commentsdiv', 'cf7storetodbs', 'normal'); // Comments Metabox
            remove_meta_box('postcustom', 'cf7storetodbs', 'normal'); // Custom Fields Metabox
            remove_meta_box('postexcerpt', 'cf7storetodbs', 'normal'); // Excerpt Metabox
            remove_meta_box('revisionsdiv', 'cf7storetodbs', 'normal'); // Revisions Metabox
            remove_meta_box('slugdiv', 'cf7storetodbs', 'normal'); // Slug Metabox
            remove_meta_box('trackbacksdiv', 'cf7storetodbs', 'normal'); // Trackback Metabox
            remove_meta_box('submitdiv', 'cf7storetodbs', 'side');
        }

        /*
         * Column For Referred Page
         */

        function cf7storetodbs_my_cpt_referred_columns($columns) {
            $columns["referedpage"] = __('Referred Page/Post', CF7STDBL_TXT_DOMAIN);
            return $columns;
        }

        /*
         * Column For Referred column values
         */

        function cf7stdbl_my_cpt_referred_column($colname, $cptid) {
            if ($colname == 'referedpage') {
                $cf7stdb_cf7_entries = get_post_meta($cptid, 'cf7stdb_cf7_entries', true);
                $refered_id = isset($cf7stdb_cf7_entries['_wpcf7_container_post']) && !empty($cf7stdb_cf7_entries['_wpcf7_container_post']) ? esc_attr($cf7stdb_cf7_entries['_wpcf7_container_post']) : '';
                ?>
                <a href="<?php echo get_edit_post_link($refered_id); ?>">
                    <?php echo esc_html(get_the_title($refered_id)); ?>
                </a>
                <?php
            }
        }

        /*
         * Column For Status
         */

        function cf7storetodbs_my_cpt_status_columns($columns) {
            $columns["status"] = __('Status', CF7STDBL_TXT_DOMAIN);
            return $columns;
        }

        /*
         * Column For Status values
         */

        function cf7storetodbs_my_cpt_status_column($colname, $cptid) {
            if ($colname == 'status') {
                $read_stat = get_post_meta($cptid, 'cf7stdb_cf7_stat', true);
                /* @var $form_stat type */
                if (isset($read_stat) && $read_stat == 'read'):
                    ?>
                    <span class="cf7stdb-form-stat cf7stdb-stat-read">
                        <?php echo __(esc_attr($read_stat), CF7STDBL_TXT_DOMAIN); ?>
                    </span>
                    <?php
                else:
                    ?>
                    <span class="cf7stdb-form-stat cf7stdb-stat-unread">
                        <?php echo __(esc_attr($read_stat), CF7STDBL_TXT_DOMAIN); ?>
                    </span>
                    <?php
                endif;
            }
        }

        /*
         * Column For Referred Contact form 7 title
         */

        function cf7storetodbs_my_cpt_referred_cf7_columns($columns) {
            $columns["cf7_title"] = __('Referred CF7 Form', CF7STDBL_TXT_DOMAIN);
            return $columns;
        }

        /*
         * Column For Referred Contact form 7 title value
         */

        function cf7storetodbs_my_cpt_referred_cf7_column($colname, $cptid) {
            if ($colname == 'cf7_title') {
                $cf7stdb_cf7_id = get_post_meta($cptid, 'cf7stdb_cf7_id', true);
                ?>
                <a href="<?php echo admin_url() . '/admin.php?page=wpcf7&post=' . $cf7stdb_cf7_id . '&action=edit'; ?>">
                    <?php echo esc_attr(get_the_title($cf7stdb_cf7_id)); ?>
                </a>
                <?php
            }
        }

        /**
         * Adds "Export As CSV" button on Entries list page
         */
        function cd7stdbl_entry_status() {
            global $current_screen, $pagenow, $post_type;
            $cd7stdb_options = get_option('cf7stdb_settings');
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'cd7stdbl-admin-ajax-nonce') && current_user_can('manage_options')) {
                $post_id = sanitize_text_field(intval($_POST['post_id']));
                $wpcf7stdb_stat = 'read';
                update_post_meta($post_id, 'cf7stdb_cf7_stat', $wpcf7stdb_stat);
                $cf7stdb_changed_stat = get_post_meta($post_id, 'cf7stdb_cf7_stat', true);
                echo $cf7stdb_changed_stat;
            }
            die();
        }

    }

    new CF7STDBL_Admin();
}