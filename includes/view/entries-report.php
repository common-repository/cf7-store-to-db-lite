<?php
defined('ABSPATH') or die('No script kiddies please!');
?>

<?php
$cf7_posts = $this->cf7stdbl_contact_form_7_plugin_posts();
$cf7stdb_posts = $this->cf7stdbl_contact_form_plugin_posts();
$cf7stdb_options = get_option('cf7stdb_settings');
$entries_year_specific = !empty($_GET['cf7_year']) ? intval($_GET['cf7_year']) : '2018';
$entries_array = array();
foreach ($cf7stdb_posts as $cf7stdb_post_key => $cf7stdb_post_val):
    $cf7_id = get_post_meta($cf7stdb_post_key, 'cf7stdb_cf7_id', true);
    array_push($entries_array, $cf7_id);
endforeach;
$cf7_post_array = array();
foreach ($cf7_posts as $cf7stdb_key => $cf7stdb_val):
    array_push($cf7_post_array, $cf7stdb_key);
endforeach;
$file_header_stamp = time();
$filename = __('cf7stdb-entry-report-' . $file_header_stamp . '', CF7STDBL_TXT_DOMAIN);
?>

<div class="cd7stdb-wrapper cd7stdb-clear">
    <div class="cd7stdb-head">     
        <?php include(CF7STDBL_PATH . 'includes/view/header.php'); ?> 
    </div> 
    <div class="cd7stdb-inner-wrapper" id="poststuff">
        <div id="post-body-full" class="metabox-holder columns-2">
            <div id="post-body-content" class="cf7stdb-primary-left-container">
                <div class="postbox">
                    <div class="cd7stdb-menu-option-wrapper clearfix" id="col-container">
                        <div class="inside" id="cd7stdb-reports-setting-wrapper">
                            <div class="cd7stdb-header-title cd7stdb-menu-option-header-title">
                                <h3><?php _e('Entries Report', CF7STDBL_TXT_DOMAIN); ?></h3>
                            </div>
                            <form action="<?php echo admin_url() . 'admin-post.php' ?>" method='post' id="cd7stdb-menu-option-form">     
                                <input type="hidden" name="action" value="cd7stdbl_generate_cd7stdb_report_options" />
                                <div class="cd7stdb-entry-report-filter-wrap">
                                    <div class="cd7stdb-option-inner-input">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                        <label>
                                            <?php _e('Select Year', CF7STDBL_TXT_DOMAIN); ?>:
                                        </label>
                                        <select id="entries-post-year-specific" name="entries_year_specific">
                                            <option value="2019" <?php echo isset($entries_year_specific) && $entries_year_specific == 2019 ? 'selected="selected"' : ''; ?>><?php _e('2019', CF7STDBL_TXT_DOMAIN); ?></option>
                                            <option value="2020" <?php echo isset($entries_year_specific) && $entries_year_specific == 2020 ? 'selected="selected"' : ''; ?>><?php _e('2020', CF7STDBL_TXT_DOMAIN); ?></option>
                                            <option value="2021" <?php echo isset($entries_year_specific) && $entries_year_specific == 2021 ? 'selected="selected"' : ''; ?>><?php _e('2021', CF7STDBL_TXT_DOMAIN); ?></option>
                                            <option value="2022" <?php echo isset($entries_year_specific) && $entries_year_specific == 2022 ? 'selected="selected"' : ''; ?>><?php _e('2022', CF7STDBL_TXT_DOMAIN); ?></option>
                                            <option value="2023" <?php echo isset($entries_year_specific) && $entries_year_specific == 2023 ? 'selected="selected"' : ''; ?>><?php _e('2023', CF7STDBL_TXT_DOMAIN); ?></option>
                                            <option value="2024" <?php echo isset($entries_year_specific) && $entries_year_specific == 2024 ? 'selected="selected"' : ''; ?>><?php _e('2024', CF7STDBL_TXT_DOMAIN); ?></option>
                                            <option value="2025" <?php echo isset($entries_year_specific) && $entries_year_specific == 2025 ? 'selected="selected"' : ''; ?>><?php _e('2025', CF7STDBL_TXT_DOMAIN); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <?php wp_nonce_field('cd7stdbl_generate_cd7stdb_rep_nonce', 'cd7stdbl_generate_cd7stdb_rep_add_nonce'); ?>
                                <div class="cd7stdb-submit-wrap">
                                    <input type="submit" class="button-primary" id="cd7stdb-generate-report-button" name='cd7stdbl_generate_report_button' value="<?php _e('Generate Report', CF7STDBL_TXT_DOMAIN); ?>" />                                   
                                    <span class="spinner cf7stdb-report-load-wrap is-active" style="display:none;"></span>
                                </div>
                                <?php
                                $form_data = array();
                                $form_title_data = array();
                                $month_arrays = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
                                if ((isset($_GET['action']) && $_GET['action'] == 'indiv_report') || !isset($_GET['action'])) {
                                    $date_month_flag = 'month';
                                    if (!empty($cf7_posts)) {
                                        foreach ($cf7_posts as $cf7_posts_key => $cf7_posts_val):
                                            foreach ($month_arrays as $month_array_val):
                                                $args = array(
                                                    'post_type' => 'cf7storetodbs',
                                                    'order' => 'ASC',
                                                    'meta_query' => array(
                                                        'relation' => 'OR',
                                                        array(
                                                            'key' => 'cf7stdb_cf7_id',
                                                            'value' => $cf7_posts_key,
                                                            'compare' => '='
                                                        )
                                                    ),
                                                    'date_query' => array(
                                                        array(
                                                            'year' => $entries_year_specific,
                                                            'month' => $month_array_val
                                                        ),
                                                    )
                                                );
                                                $query = new WP_Query($args);
                                                $cf7_title = get_the_title($cf7_posts_key);
                                                array_push($form_title_data, $cf7_title);
                                                $count = $query->post_count;
                                                array_push($form_data, $count);
                                                wp_reset_postdata();
                                            endforeach;
                                        endforeach;
                                    }
                                    $form_title_data_chunk = array_chunk($form_title_data, 12);
                                    $form_data_chunk = array_chunk($form_data, 12);
                                    foreach ($form_data_chunk as $form_data_chunky => $form_data_chunki):
                                        foreach ($form_title_data_chunk as $form_title_data_chunky => $form_title_data_chunki):
                                            if ($form_title_data_chunky == $form_data_chunky):
                                                $datasets[] = [
                                                    'label' => $form_title_data_chunki[0],
                                                    'fill' => false,
                                                    'borderColor' => 'rgba(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ', 1)',
                                                    'borderWidth' => 1.3,
                                                    'data' => $form_data_chunki
                                                ];
                                            endif;
                                        endforeach;
                                    endforeach;
                                }

                                $datasets = isset($datasets) && !empty($datasets)?json_encode($datasets):'';
                                ?>
                                <div class="cd7stdb-report-wrap">
                                    <canvas 
                                    id="chart_0" 
                                    height="200" 
                                    width="600"
                                    data-flag-value ="<?php echo esc_attr($date_month_flag); ?>"
                                    data-total-submission='<?php echo esc_attr($datasets); ?>' 
                                    data-label-text="<?php _e('No. of Entries', CF7STDBL_TXT_DOMAIN); ?>"
                                    data-label-header="<?php _e('Report On Contact Form 7 Entries', CF7STDBL_TXT_DOMAIN); ?>"
                                    data-entry-img-label="<?php echo esc_attr($filename); ?>"
                                    ></canvas>
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
<!-- 'backgroundColor' => 'rgba(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ', 0.3)', -->