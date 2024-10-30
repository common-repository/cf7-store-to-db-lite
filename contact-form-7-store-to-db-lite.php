<?php

defined('ABSPATH') or die('No script kiddies please!!');
/*
  Plugin Name: Form Store to DB
  Plugin URI: http://accesspressthemes.com/wordpress-plugins/contact-form-7-store-to-db-lite
  Description: Plugin to easily store and manage contact form 7 entries manually into the WordPress without loosing the data.
  Version: 	1.1.1
  Author:  	AccessPress Themes
  Author URI:  http://accesspressthemes.com
  License: 	GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /languages
  Text Domain: contact-form-7-store-to-db-lite
 */

/**
 * Plugin Main Class
 *
 * @since 1.0.0
 */
if (!class_exists('Contact_form_7_store_to_db_lite')) {

    class Contact_form_7_store_to_db_lite {

        /**
         * Plugin Main initialization
         *
         * @since 1.0.0
         */
        function __construct() {
            $this->define_constants();
            $this->includes();
        }

        /**
         * Necessary Constants Define
         *
         * @since 1.0.0
         */
        function define_constants() {
            global $wpdb;
            defined('CF7STDBL_PATH') or define('CF7STDBL_PATH', plugin_dir_path(__FILE__));
            defined('CF7STDBL_URL') or define('CF7STDBL_URL', plugin_dir_url(__FILE__));
            defined('CF7STDBL_IMG_DIR') or define('CF7STDBL_IMG_DIR', plugin_dir_url(__FILE__) . 'images/');
            defined('CF7STDBL_ATTACHMENT_DIR') or define('CF7STDBL_ATTACHMENT_DIR', plugin_dir_url(__FILE__) . 'image/');
            defined('CF7STDBL_CSS_DIR') or define('CF7STDBL_CSS_DIR', plugin_dir_url(__FILE__) . 'includes/css/');
            defined('CF7STDBL_JS_DIR') or define('CF7STDBL_JS_DIR', plugin_dir_url(__FILE__) . 'includes/js/');
            defined('CF7STDBL_VERSION') or define('CF7STDBL_VERSION', '1.1.1');
            defined('CF7STDBL_TXT_DOMAIN') or define('CF7STDBL_TXT_DOMAIN', 'contact-form-7-store-to-db-lite');
        }

        /**
         * Includes all the necessary files
         *
         * @since 1.0.0
         */
        function includes() {
            include(CF7STDBL_PATH . 'includes/class/cf7stdb-library.php');
            include(CF7STDBL_PATH . 'includes/class/cf7stdb-activate.php');
            include(CF7STDBL_PATH . 'includes/class/cf7stdb-admin.php');
        }

    }

    $GLOBALS['Contact_form_7_store_to_db_lite'] = new Contact_form_7_store_to_db_lite();
    $GLOBALS['cf7stdb_settings'] = get_option('cf7stdb_settings');
}