<?php

defined('ABSPATH') or die('No script kiddies please!!');
if (!class_exists('CF7STDBL_Activation')) {

    class CF7STDBL_Activation {

        /**
         * Executes all the tasks on plugin activation
         * 
         * @since 1.0.0
         */
        function __construct() {
            register_activation_hook(CF7STDBL_PATH . 'contact-form-7-store-to-db-lite.php', array($this, 'register_activate_plugin'));
            add_action('init', array($this, 'register_post_type'));
        }

        /**
         * All the activation tasks
         * 
         * @since 1.0.0
         */
        function register_activate_plugin() {
            $this->update_initial_data_to_op_table();
        }

        /**
         * Register Custom post type
         * 
         * @since 1.0.0
         */
        function register_post_type() {
            include( CF7STDBL_PATH . 'includes/require/register-post.php' );
            register_post_type('CF7 Store to DBs', $args);
        }

        /**
         * Updating initial default values into database
         *
         * @since 1.0.0
         */
        function update_initial_data_to_op_table() {
            $cd7stdb_general_setting = array(
                'cd7stdb_enable_disable' => 'on'
            );
            if (!get_option('cf7stdb_settings')) {
                update_option('cf7stdb_settings', $cd7stdb_general_setting);
            }
        }

    }

    new CF7STDBL_Activation();
}