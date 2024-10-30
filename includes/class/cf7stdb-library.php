<?php

defined('ABSPATH') or die('No script kiddies please!!');
if (!class_exists('CF7STDBL_Library')) {

    class CF7STDBL_Library {

        /**
         * Prints array in pre format
         *
         * @since 1.0.0
         *
         * @param array $array
         */
        function print_array($array) {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        }

        /**
         * Generates random string
         *
         * @param int $length
         * @return string
         *
         * @since 1.0.0
         */
        function cf7stdbl_generate_random_string($length) {
            $string = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $random_string = '';
            for ($i = 1; $i <= $length; $i++) {
                $random_string .= $string[rand(0, 61)];
            }
            return $random_string;
        }

        /**
         * Get IP adress of the form submission
         *
         * @param int $length
         * @return string
         *
         * @since 1.0.0
         */
        function cf7stdbl_getip() {
            $ipaddress = '';
            if (getenv('HTTP_CLIENT_IP'))
                $ipaddress = getenv('HTTP_CLIENT_IP');
            else if (getenv('HTTP_X_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            else if (getenv('HTTP_X_FORWARDED'))
                $ipaddress = getenv('HTTP_X_FORWARDED');
            else if (getenv('HTTP_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_FORWARDED_FOR');
            else if (getenv('HTTP_FORWARDED'))
                $ipaddress = getenv('HTTP_FORWARDED');
            else if (getenv('REMOTE_ADDR'))
                $ipaddress = getenv('REMOTE_ADDR');
            else
                $ipaddress = 'UNKNOWN';

            return $ipaddress;
        }

        /**
         * Get the browser Header.
         * @see http://stackoverflow.com/a/20934782/4255615
         * @return string Browser name
         */
        function cf7stdbl_browser_header() {

            $ua = $_SERVER['HTTP_USER_AGENT'];

            if (
                strpos(strtolower($ua), 'safari/') &&
                strpos(strtolower($ua), 'opr/')
            ) {
                // Opera
                $res = 'Opera';
            } elseif (
                strpos(strtolower($ua), 'safari/') &&
                strpos(strtolower($ua), 'chrome/')
            ) {
                // Chrome
                $res = 'Chrome';
            } elseif (
                strpos(strtolower($ua), 'msie') ||
                strpos(strtolower($ua), 'trident/')
            ) {
                // Internet Explorer
                $res = 'Internet Explorer';
            } elseif (strpos(strtolower($ua), 'firefox/')) {
                // Firefox
                $res = 'Firefox';
            } elseif (
                strpos(strtolower($ua), 'safari/') &&
                (strpos(strtolower($ua), 'opr/') === false) &&
                (strpos(strtolower($ua), 'chrome/') === false)
            ) {
                // Safari
                $res = 'Safari';
            } else {
                // Out of data
                $res = false;
            }

            return $res;
        }

        /**
         * Get the current page id
         * @return integer current page id
         */
        function cf7stdbl_current_page_id() {
            global $wp_query, $post;
            if (!is_404()) {
                $current_page_id = $wp_query->post->ID;
            } else {
                $current_page_id = '1111';
            }
        }

        /**
         * Get filter the mail content message
         * @return $message_body
         */
        function cf7stdbl_filter_the_message($email_message_body_fincont) {
            $email_message_body_filcont = apply_filters('the_content', $email_message_body_fincont);
            /* @var $filtered_message type */
            return $email_message_body_filcont;
        }

        /**
         * Get post type of the contact form 7 plugin
         * @return $message_body
         */
        function cf7stdbl_contact_form_plugin_posts() {
            $contact_form_7_qry = new WP_Query(array(
                'post_type' => 'cf7storetodbs'
            )
        );
            $contact_form_7_post_array = array();
            if ($contact_form_7_qry->have_posts()) {
                while ($contact_form_7_qry->have_posts()) {
                    $contact_form_7_qry->the_post();
                    $contact_form_id = get_the_id();
                    $contact_form_title = get_the_title();
                    $contact_form_7_post_array[$contact_form_id] = $contact_form_title;
                }
            }
            return $contact_form_7_post_array;
        }

        /**
         * Get post type of the contact form 7 plugin posts
         * @return $message_body
         */
        function cf7stdbl_contact_form_7_plugin_posts() {
            $contact_form_7_qry = new WP_Query(array(
                'post_type' => 'wpcf7_contact_form'
            )
        );
            $contact_form_7_post_array = array();
            if ($contact_form_7_qry->have_posts()) {
                while ($contact_form_7_qry->have_posts()) {
                    $contact_form_7_qry->the_post();
                    $contact_form_id = get_the_id();
                    $contact_form_title = get_the_title();
                    $contact_form_7_post_array[$contact_form_id] = $contact_form_title;
                }
            }
            return $contact_form_7_post_array;
        }

        function cf7stdbl_sanitize_array($array = array(), $sanitize_rule = array()) {
            if ( !is_array($array) || count($array) == 0 ) {
                return array();
            }
            foreach ( $array as $k => $v ) {
                if ( !is_array($v) ) {
                    $default_sanitize_rule = (is_numeric($k)) ? 'html' : 'text';
                    $sanitize_type = isset($sanitize_rule[ $k ]) ? $sanitize_rule[ $k ] : $default_sanitize_rule;
                    $array[ $k ] = $this->cf7stdbl_sanitize_value($v, $sanitize_type);
                }
                if ( is_array($v) ) {
                    $array[ $k ] = $this->cf7stdbl_sanitize_array($v, $sanitize_rule);
                }
            }
            return $array;
        }

        function cf7stdbl_sanitize_value($value = '', $sanitize_type = 'text') {
            switch ( $sanitize_type ) {
                case 'html':
                $allowed_html = wp_kses_allowed_html('post');
                return wp_kses($value, $allowed_html);
                break;
                default:
                return sanitize_text_field($value);
                break;
            }
        }
    }
}